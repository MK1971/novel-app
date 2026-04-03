<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Payment;
use App\Support\AchievementUnlock;
use App\Support\AdminNotifier;
use App\Support\ChapterLifecycle;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    private const USER_PAYPAL_CONFIG_MESSAGE = 'Edit checkout is temporarily unavailable. Please try again later. If this keeps happening, contact the site administrator.';

    public function cancel(Request $request)
    {
        $chapterId = $request->query('chapter_id');
        $editId = $request->query('edit_id');
        if (! $chapterId || ! $editId) {
            return redirect()->route('chapters.index')->with(
                'warning',
                'Payment was not completed. You can return to a chapter and try checkout again when you are ready.'
            );
        }

        $edit = Edit::whereKey($editId)->where('user_id', auth()->id())->first();
        if ($edit && $edit->status === 'pending_payment' && (int) $edit->chapter_id === (int) $chapterId) {
            // Keep draft; user can resume from the chapter page.
        }

        return redirect()->route('chapters.show', $chapterId)
            ->with(
                'warning',
                'Payment was not completed, so you were not charged. Your suggestion is still saved on this page — use Resume checkout or Submit & Pay $2 to try again.'
            );
    }

    public function checkout(Request $request)
    {
        if ($request->filled('resume_edit_id')) {
            return $this->checkoutResume($request);
        }

        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'type' => 'required|in:writing,phrase,inline_edit',
        ]);

        if ($request->type === 'inline_edit') {
            $request->validate([
                'paragraph_number' => 'required|integer|min:0',
                'original_text' => 'required|string',
                'suggested_text' => 'required|string',
                'reason' => 'nullable|string',
            ]);
        } else {
            $request->validate([
                'edited_text' => 'required|string',
            ]);
        }

        if (! self::paypalCredentialsConfigured()) {
            Log::warning('PayPal checkout blocked: missing API credentials for current mode', [
                'mode' => config('paypal.mode'),
            ]);

            return back()->with('error', self::USER_PAYPAL_CONFIG_MESSAGE);
        }

        $chapter = Chapter::findOrFail($request->chapter_id);
        if ($response = $this->assertChapterAllowsPaidEdits($chapter)) {
            return $response;
        }

        $payloadJson = null;
        if ($request->type === 'inline_edit') {
            $payloadJson = json_encode([
                'paragraph_number' => (int) $request->paragraph_number,
                'original_text' => $request->original_text,
                'suggested_text' => $request->suggested_text,
                'reason' => $request->reason ?? '',
            ]);
            session(['inlineEditData' => $payloadJson]);
        } else {
            session()->forget('inlineEditData');
        }

        try {
            $edit = $this->findOrSyncPendingPaymentEdit($request, $chapter, $payloadJson);
        } catch (QueryException $e) {
            Log::error('payment.checkout database error', ['message' => $e->getMessage()]);

            return back()->with('error', self::friendlyDatabaseErrorMessage($e));
        }

        session(['edit_type' => $request->type]);

        return $this->startPayPalCheckout($chapter, $edit);
    }

    private function checkoutResume(Request $request): RedirectResponse
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'resume_edit_id' => 'required|exists:edits,id',
        ]);

        if (! self::paypalCredentialsConfigured()) {
            Log::warning('PayPal checkout resume blocked: missing API credentials for current mode', [
                'mode' => config('paypal.mode'),
            ]);

            return back()->with('error', self::USER_PAYPAL_CONFIG_MESSAGE);
        }

        $chapter = Chapter::findOrFail($request->chapter_id);
        if ($response = $this->assertChapterAllowsPaidEdits($chapter)) {
            return $response;
        }

        $edit = Edit::query()
            ->whereKey($request->resume_edit_id)
            ->where('user_id', $request->user()->id)
            ->where('chapter_id', $chapter->id)
            ->where('status', 'pending_payment')
            ->firstOrFail();

        if ($edit->type === 'inline_edit' && $edit->inline_edit_payload) {
            session(['inlineEditData' => $edit->inline_edit_payload]);
        }
        session(['edit_type' => $edit->type]);

        return $this->startPayPalCheckout($chapter, $edit);
    }

    private function findOrSyncPendingPaymentEdit(Request $request, Chapter $chapter, ?string $payloadJson): Edit
    {
        $userId = $request->user()->id;
        $existing = Edit::query()
            ->where('user_id', $userId)
            ->where('chapter_id', $chapter->id)
            ->where('status', 'pending_payment')
            ->first();

        if ($request->type === 'inline_edit') {
            if ($existing) {
                $existing->update([
                    'type' => 'inline_edit',
                    'original_text' => '',
                    'edited_text' => 'Inline edit pending',
                    'inline_edit_payload' => $payloadJson,
                ]);

                return $existing->fresh();
            }

            return Edit::create([
                'user_id' => $userId,
                'chapter_id' => $chapter->id,
                'type' => 'inline_edit',
                'original_text' => '',
                'edited_text' => 'Inline edit pending',
                'status' => 'pending_payment',
                'inline_edit_payload' => $payloadJson,
            ]);
        }

        if ($existing) {
            $existing->update([
                'type' => $request->type,
                'original_text' => $chapter->content,
                'edited_text' => $request->string('edited_text')->value(),
                'inline_edit_payload' => null,
            ]);
            session()->forget('inlineEditData');

            return $existing->fresh();
        }

        return Edit::create([
            'user_id' => $userId,
            'chapter_id' => $chapter->id,
            'type' => $request->type,
            'original_text' => $chapter->content,
            'edited_text' => $request->edited_text,
            'status' => 'pending_payment',
        ]);
    }

    private function assertChapterAllowsPaidEdits(Chapter $chapter): ?RedirectResponse
    {
        $chapter->loadMissing('book');
        if (ChapterLifecycle::isPeterTrullChapter($chapter)) {
            return back()->with(
                'error',
                'Paid edits apply to The Book With No Name only. Peter Trull is voting-only.'
            );
        }

        if (ChapterLifecycle::isTbwChapter($chapter) && ChapterLifecycle::suggestionsClosedForTbwChapter($chapter)) {
            return back()->with('error', 'The editing window for this chapter has closed.');
        }

        return null;
    }

    private function startPayPalCheckout(Chapter $chapter, Edit $edit): RedirectResponse
    {
        if ($response = $this->assertChapterAllowsPaidEdits($chapter)) {
            return $response;
        }

        $chapterId = $chapter->id;
        $editId = $edit->id;

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('payment.success', ['chapter_id' => $chapterId, 'edit_id' => $editId]),
                    'cancel_url' => route('payment.cancel', ['chapter_id' => $chapterId, 'edit_id' => $editId]),
                ],
                'purchase_units' => [
                    [
                        'description' => 'Suggest Edit - '.$chapter->displayTitle().' - The Book With No Name',
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => '2.00',
                        ],
                    ],
                ],
            ]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            $summary = self::summarizePayPalResponse(is_array($response) ? $response : []);
            Log::error('PayPal createOrder failed', ['response' => $response]);

            return back()->with('error', 'Could not start PayPal checkout. '.$summary);
        } catch (\Throwable $e) {
            Log::error('PayPal checkout exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Could not reach PayPal: '.$e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $token = $request->query('token');
        $chapterId = $request->query('chapter_id');
        $editId = $request->query('edit_id');
        if (! $token || ! $chapterId || ! $editId) {
            return redirect()->route('chapters.index')->with('error', 'Your payment session was incomplete. Please start checkout again from the chapter page.');
        }

        $edit = Edit::find($editId);
        if (! $edit || $edit->status !== 'pending_payment' || $edit->user_id !== $request->user()->id) {
            return redirect()->route('chapters.index')->with('error', 'That payment session is no longer valid. If you paid, contact support with your PayPal receipt.');
        }

        if (! self::paypalCredentialsConfigured()) {
            Log::warning('PayPal capture blocked: missing API credentials for current mode', [
                'mode' => config('paypal.mode'),
            ]);

            return redirect()->route('chapters.show', $chapterId)->with('error', self::USER_PAYPAL_CONFIG_MESSAGE);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $captureResponse = $provider->capturePaymentOrder($token);
        } catch (\Throwable $e) {
            Log::error('PayPal capture exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->route('chapters.show', $chapterId)->with(
                'error',
                'We could not confirm your payment with PayPal. If you were charged, contact support with your receipt.'
            );
        }

        if (! is_array($captureResponse)) {
            Log::error('PayPal capturePaymentOrder returned non-array', ['response' => $captureResponse]);

            return redirect()->route('chapters.show', $chapterId)->with(
                'error',
                'Payment confirmation failed. Please try again or contact support if the charge appears on your account.'
            );
        }

        if (isset($captureResponse['status']) && $captureResponse['status'] === 'COMPLETED') {
            $edit->refresh();

            try {
                $payment = Payment::create([
                    'user_id' => $request->user()->id,
                    'amount_cents' => 200,
                    'payment_id' => $token,
                    'status' => 'completed',
                    'edit_id' => $editId,
                ]);

                if ($edit->type === 'inline_edit') {
                    $raw = $edit->inline_edit_payload;
                    if ($raw === null || $raw === '') {
                        $sessionRaw = session('inlineEditData');
                        $raw = is_string($sessionRaw) ? $sessionRaw : null;
                    }
                    $inlineEditArray = self::decodeInlineEditPayloadRaw($raw);
                    if (! is_array($inlineEditArray)) {
                        Log::critical('Paid inline edit missing or invalid payload', [
                            'edit_id' => $edit->id,
                            'user_id' => $request->user()->id,
                            'payload_len' => is_string($raw) ? strlen($raw) : null,
                        ]);

                        return redirect()->route('chapters.show', $chapterId)->with(
                            'error',
                            'Your payment was recorded, but we could not restore your paragraph suggestion. Please contact support with your PayPal receipt so we can fix this.'
                        );
                    }

                    InlineEdit::create([
                        'user_id' => $request->user()->id,
                        'chapter_id' => (int) $chapterId,
                        'paragraph_number' => (int) ($inlineEditArray['paragraph_number'] ?? 0),
                        'original_text' => (string) ($inlineEditArray['original_text'] ?? ''),
                        'suggested_text' => (string) ($inlineEditArray['suggested_text'] ?? ''),
                        'reason' => (string) ($inlineEditArray['reason'] ?? ''),
                        'status' => 'pending',
                        'payment_id' => $payment->id,
                    ]);
                    session()->forget('inlineEditData');
                    $edit->update([
                        'inline_edit_payload' => null,
                    ]);
                }

                $edit->update(['status' => 'pending']);

                $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $chapterId]);
                $stats->increment('total_edits');

                AchievementUnlock::syncForUser($request->user());

                $chapterForMail = Chapter::with('book')->find($chapterId);
                if ($chapterForMail) {
                    $label = $edit->type === 'inline_edit' ? 'Paragraph suggestion' : 'Full-chapter suggestion';
                    $bookName = $chapterForMail->book?->name ?? 'Unknown book';
                    $readerLabel = $chapterForMail->headingPrefix().': '.$chapterForMail->displayTitle();
                    AdminNotifier::notifyNewPaidSuggestion(
                        "{$label} pending review — {$bookName}, {$readerLabel} (row id {$chapterForMail->id}, edit #{$edit->id}) by {$request->user()->name}."
                    );
                }
            } catch (QueryException $e) {
                Log::error('payment.success database error', [
                    'message' => $e->getMessage(),
                    'chapter_id' => $chapterId,
                    'edit_id' => $editId,
                ]);

                return redirect()->route('chapters.show', $chapterId)->with(
                    'error',
                    self::friendlyDatabaseErrorMessage($e)
                );
            }

            return redirect()
                ->to(route('chapters.show', $chapterId).'#edit-submission-box')
                ->with(
                    'success',
                    'Thank you for your submission! Your payment was accepted and your edit has been submitted for review. You now have one vote credit on Peter Trull Solitary Detective (open Vote in the menu when you are ready).'
                );
        }

        $summary = self::summarizePayPalResponse($captureResponse);
        Log::error('PayPal capturePaymentOrder not completed', ['response' => $captureResponse]);

        return redirect()->route('chapters.show', $chapterId)->with(
            'error',
            'Payment could not be completed. '.$summary
        );
    }

    /**
     * Restore paragraph suggestion JSON from DB column or session (string).
     *
     * @return array<string, mixed>|null
     */
    private static function decodeInlineEditPayloadRaw(mixed $raw): ?array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (! is_string($raw)) {
            return null;
        }
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return null;
        }
        $flags = JSON_INVALID_UTF8_IGNORE | JSON_BIGINT_AS_STRING;
        $decoded = json_decode($trimmed, true, 512, $flags);
        if (is_array($decoded)) {
            return $decoded;
        }
        $decoded = json_decode(stripslashes($trimmed), true, 512, $flags);

        return is_array($decoded) ? $decoded : null;
    }

    private static function friendlyDatabaseErrorMessage(QueryException $e): string
    {
        $m = $e->getMessage();
        if (
            str_contains($m, 'inline_edit_payload')
            || str_contains($m, 'Unknown column')
            || str_contains($m, 'no such column')
        ) {
            return 'The database is missing a required update. Run php artisan migrate on the server, then try checkout again.';
        }
        if (str_contains($m, 'foreign key') || str_contains($m, 'FOREIGN KEY constraint')) {
            return 'This checkout no longer matches saved data (for example the chapter was removed). Open Chapters and start a new suggestion, then pay again.';
        }

        return 'A database error blocked checkout. After deploying, run php artisan migrate. If it still fails, contact support with the time you tried to pay.';
    }

    private static function paypalCredentialsConfigured(): bool
    {
        $mode = strtolower((string) config('paypal.mode', 'sandbox'));
        $group = $mode === 'live' ? 'live' : 'sandbox';
        $creds = config("paypal.{$group}", []);
        $id = trim((string) ($creds['client_id'] ?? ''));
        $secret = trim((string) ($creds['client_secret'] ?? ''));

        return $id !== '' && $secret !== '';
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private static function summarizePayPalResponse(array $response): string
    {
        if (isset($response['message']) && is_string($response['message']) && $response['message'] !== '') {
            return self::truncateUserMessage($response['message']);
        }

        if (isset($response['error'])) {
            $err = $response['error'];
            if (is_string($err) && $err !== '') {
                return self::truncateUserMessage($err);
            }
            if (is_array($err)) {
                if (isset($err['message']) && is_string($err['message']) && $err['message'] !== '') {
                    return self::truncateUserMessage($err['message']);
                }
                if (isset($err['details']) && is_string($err['details'])) {
                    return self::truncateUserMessage($err['details']);
                }
            }
        }

        if (isset($response['details']) && is_array($response['details']) && $response['details'] !== []) {
            $first = $response['details'][0] ?? null;
            if (is_array($first) && isset($first['description']) && is_string($first['description'])) {
                return self::truncateUserMessage($first['description']);
            }
        }

        if (isset($response['name']) && is_string($response['name']) && $response['name'] !== '') {
            return self::truncateUserMessage($response['name']);
        }

        return 'Please try again, or contact support if the problem continues.';
    }

    private static function truncateUserMessage(string $message): string
    {
        $message = trim(preg_replace('/\s+/', ' ', $message) ?? $message);
        if (strlen($message) <= 220) {
            return $message;
        }

        return substr($message, 0, 217).'…';
    }
}
