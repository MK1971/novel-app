<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\Payment;
use App\Models\User;
use App\Support\AchievementUnlock;
use App\Support\AdminNotifier;
use App\Support\ChapterLifecycle;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    private const USER_PAYPAL_CONFIG_MESSAGE = 'Edit checkout is unavailable because PayPal credentials are missing for the active PAYPAL_MODE. Set PAYPAL_SANDBOX_CLIENT_ID/SECRET (or PAYPAL_LIVE_CLIENT_ID/SECRET, or PAYPAL_CLIENT_ID/SECRET fallback) and run php artisan optimize:clear, then try again.';

    private const DONATION_MIN_CENTS = 200;

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
        if ($response = $this->throttleCheckoutAttempts($request)) {
            return $response;
        }

        if ($request->filled('resume_edit_id')) {
            return $this->checkoutResume($request);
        }

        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'type' => 'required|in:writing,phrase,inline_edit',
            'show_in_public_feed' => 'nullable|boolean',
            'queue_only' => 'nullable|boolean',
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
        $showInPublicFeed = $request->has('show_in_public_feed')
            ? $request->boolean('show_in_public_feed')
            : true;
        if ($request->type === 'inline_edit') {
            $payloadJson = json_encode([
                'paragraph_number' => (int) $request->paragraph_number,
                'original_text' => $request->original_text,
                'suggested_text' => $request->suggested_text,
                'reason' => $request->reason ?? '',
                'show_in_public_feed' => $showInPublicFeed,
            ]);
            session(['inlineEditData' => $payloadJson]);
        } else {
            session()->forget('inlineEditData');
        }

        try {
            $hasExistingQueuedEdits = Edit::query()
                ->where('user_id', $request->user()->id)
                ->where('status', 'pending_payment')
                ->exists();
            $appendAsNewEdit = $request->boolean('queue_only') || $hasExistingQueuedEdits;

            $edit = $this->findOrSyncPendingPaymentEdit(
                $request,
                $chapter,
                $payloadJson,
                $showInPublicFeed,
                $appendAsNewEdit
            );
        } catch (QueryException $e) {
            Log::error('payment.checkout database error', ['message' => $e->getMessage()]);

            return back()->with('error', self::friendlyDatabaseErrorMessage($e));
        }

        session(['edit_type' => $request->type]);

        if ($request->boolean('queue_only')) {
            return redirect()
                ->route('chapters.show', $chapter->id)
                ->with('success', 'Suggestion added to your checkout queue. Add another edit or checkout all queued edits.');
        }

        return $this->startPayPalCheckout($request->user()->id, $chapter->id, $edit->id);
    }

    public function removeQueuedEdit(Request $request, int $editId): RedirectResponse
    {
        $edit = Edit::query()
            ->whereKey($editId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $edit) {
            return back()->with('warning', 'That queued edit could not be found. It may have already been removed.');
        }

        if ($edit->status !== 'pending_payment') {
            return back()->with('warning', 'Only queued (unpaid) edits can be removed.');
        }

        $chapterId = (int) $edit->chapter_id;
        $edit->delete();

        return redirect()
            ->route('chapters.show', $chapterId)
            ->with('success', 'Removed one queued edit.');
    }

    private function checkoutResume(Request $request): RedirectResponse
    {
        if ($response = $this->throttleCheckoutAttempts($request)) {
            return $response;
        }

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

        return $this->startPayPalCheckout($request->user()->id, $chapter->id, $edit->id);
    }

    private function findOrSyncPendingPaymentEdit(Request $request, Chapter $chapter, ?string $payloadJson, bool $showInPublicFeed, bool $queueOnly): Edit
    {
        $userId = $request->user()->id;

        if ($request->type === 'inline_edit') {
            return Edit::create([
                'user_id' => $userId,
                'chapter_id' => $chapter->id,
                'type' => 'inline_edit',
                'original_text' => '',
                'edited_text' => 'Inline edit pending',
                'status' => 'pending_payment',
                'inline_edit_payload' => $payloadJson,
                'show_in_public_feed' => $showInPublicFeed,
            ]);
        }

        $existing = null;
        if (! $queueOnly) {
            $existing = Edit::query()
                ->where('user_id', $userId)
                ->where('chapter_id', $chapter->id)
                ->where('status', 'pending_payment')
                ->where('type', '!=', 'inline_edit')
                ->first();
        }

        if ($existing) {
            $existing->update([
                'type' => $request->type,
                'original_text' => $chapter->content,
                'edited_text' => $request->string('edited_text')->value(),
                'inline_edit_payload' => null,
                'show_in_public_feed' => $showInPublicFeed,
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
            'show_in_public_feed' => $showInPublicFeed,
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

    private function startPayPalCheckout(int $userId, int $chapterId, int $preferredEditId): RedirectResponse
    {
        $chapter = Chapter::findOrFail($chapterId);
        if ($response = $this->assertChapterAllowsPaidEdits($chapter)) {
            return $response;
        }

        $pendingEdits = Edit::query()
            ->where('user_id', $userId)
            ->where('status', 'pending_payment')
            ->orderBy('id')
            ->get();
        if ($pendingEdits->isEmpty()) {
            return back()->with('error', 'No pending suggestions were found for checkout. Add a suggestion first.');
        }

        $pendingIds = $pendingEdits->pluck('id')->map(fn ($id) => (int) $id)->all();
        $primaryEditId = in_array($preferredEditId, $pendingIds, true) ? $preferredEditId : $pendingIds[0];
        $amountValue = number_format((count($pendingIds) * 2), 2, '.', '');
        $count = count($pendingIds);
        $idsParam = implode(',', $pendingIds);

        try {
            $response = self::withoutProxyEnvironment(function () use ($chapter, $chapterId, $primaryEditId, $amountValue, $count, $idsParam) {
                $provider = self::makePayPalProvider();
                $provider->getAccessToken();

                return $provider->createOrder([
                    'intent' => 'CAPTURE',
                    'application_context' => [
                        'return_url' => route('payment.success', [
                            'chapter_id' => $chapterId,
                            'edit_id' => $primaryEditId,
                            'pending_ids' => $idsParam,
                        ]),
                        'cancel_url' => route('payment.cancel', ['chapter_id' => $chapterId, 'edit_id' => $primaryEditId]),
                    ],
                    'purchase_units' => [
                        [
                            'description' => "Batch edit checkout ({$count} suggestions) - ".$chapter->readerHeadingLine(),
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => $amountValue,
                            ],
                        ],
                    ],
                ]);
            });

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            $summary = self::summarizePayPalResponse(is_array($response) ? $response : []);
            Log::error('PayPal createOrder failed', [
                'response' => $response,
                'network_diagnostics' => self::paypalNetworkDiagnostics(),
            ]);

            return back()->with('error', 'Could not start PayPal checkout. '.$summary);
        } catch (\Throwable $e) {
            Log::error('PayPal checkout exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'network_diagnostics' => self::paypalNetworkDiagnostics(),
            ]);
            if (self::isTunnelFailureMessage($e->getMessage())) {
                return back()->with('error', 'Could not connect to PayPal from this environment. Check outbound network/proxy settings, then try again.');
            }

            return back()->with('error', 'Could not reach PayPal right now. Please try again in a moment.');
        }
    }

    public function success(Request $request)
    {
        $token = (string) $request->query('token');
        $chapterId = (int) $request->query('chapter_id');
        $editId = (int) $request->query('edit_id');
        $pendingIds = self::decodePendingIds((string) $request->query('pending_ids'));
        if (! $token || ! $chapterId || ! $editId) {
            return redirect()->route('chapters.index')->with('error', 'Your payment session was incomplete. Please start checkout again from the chapter page.');
        }

        if ($pendingIds === []) {
            $pendingIds = [$editId];
        }

        $edits = Edit::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending_payment')
            ->whereIn('id', $pendingIds)
            ->orderBy('id')
            ->get();
        if ($edits->isEmpty()) {
            return redirect()->route('chapters.index')->with('error', 'That payment session is no longer valid. If you paid, contact support with your PayPal receipt.');
        }

        if (! self::paypalCredentialsConfigured()) {
            Log::warning('PayPal capture blocked: missing API credentials for current mode', [
                'mode' => config('paypal.mode'),
            ]);

            return redirect()->route('chapters.show', $chapterId)->with('error', self::USER_PAYPAL_CONFIG_MESSAGE);
        }

        try {
            $captureResponse = self::withoutProxyEnvironment(function () use ($token) {
                $provider = self::makePayPalProvider();
                $provider->getAccessToken();

                return $provider->capturePaymentOrder($token);
            });
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
            try {
                $processed = 0;
                DB::transaction(function () use ($request, $token, $edits, &$processed) {
                    foreach ($edits as $edit) {
                        $payment = Payment::create([
                            'user_id' => $request->user()->id,
                            'amount_cents' => 200,
                            'payment_id' => $token,
                            'status' => 'completed',
                            'purpose' => 'edit_fee',
                            'edit_id' => $edit->id,
                        ]);

                        if ($edit->type === 'inline_edit') {
                            $inlineEditArray = self::decodeInlineEditPayloadRaw($edit->inline_edit_payload);
                            if (! is_array($inlineEditArray)) {
                                throw new QueryException('', [], new \RuntimeException('Missing inline payload'));
                            }

                            InlineEdit::create([
                                'user_id' => $request->user()->id,
                                'chapter_id' => (int) $edit->chapter_id,
                                'paragraph_number' => (int) ($inlineEditArray['paragraph_number'] ?? 0),
                                'original_text' => (string) ($inlineEditArray['original_text'] ?? ''),
                                'suggested_text' => (string) ($inlineEditArray['suggested_text'] ?? ''),
                                'reason' => (string) ($inlineEditArray['reason'] ?? ''),
                                'status' => 'pending',
                                'payment_id' => $payment->id,
                                'show_in_public_feed' => (bool) ($inlineEditArray['show_in_public_feed'] ?? true),
                            ]);
                            $edit->update(['inline_edit_payload' => null]);
                        }

                        $edit->update(['status' => 'pending']);
                        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $edit->chapter_id]);
                        $stats->increment('total_edits');
                        $processed++;
                    }
                });

                session()->forget('inlineEditData');
                AchievementUnlock::syncForUser($request->user());

                $notifyKey = 'admin-notify-paid-checkout:'.$token;
                if (Cache::add($notifyKey, true, now()->addDays(7))) {
                    $chapters = $edits
                        ->map(fn (Edit $e) => Chapter::with('book')->find($e->chapter_id))
                        ->filter()
                        ->values();
                    if ($chapters->isNotEmpty()) {
                        $label = $processed > 1 ? "{$processed} suggestions" : 'Suggestion';
                        $lines = $chapters
                            ->map(fn (Chapter $c) => '- '.($c->book?->name ?? 'Unknown book').' / '.$c->readerHeadingLine().' (row id '.$c->id.')')
                            ->implode("\n");
                        AdminNotifier::notifyNewPaidSuggestion(
                            "{$label} pending review by {$request->user()->name}.\nCheckout token: {$token}\n\nItems:\n{$lines}"
                        );
                    }
                }
            } catch (\Throwable $e) {
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
                    'Thank you! Your payment was accepted and your queued suggestions were submitted for review. You now have one vote credit per paid suggestion for Peter Trull Solitary Detective.'
                );
        }

        $summary = self::summarizePayPalResponse($captureResponse);
        Log::error('PayPal capturePaymentOrder not completed', ['response' => $captureResponse]);

        return redirect()->route('chapters.show', $chapterId)->with(
            'error',
            'Payment could not be completed. '.$summary
        );
    }

    public function donationCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'amount_dollars' => ['required', 'numeric', 'min:2', 'max:10000'],
        ]);

        if (! self::paypalCredentialsConfigured()) {
            Log::warning('PayPal donation checkout blocked: missing API credentials for current mode', [
                'mode' => config('paypal.mode'),
            ]);

            return back()->with('error', 'Donations are temporarily unavailable. Please try again later.');
        }

        $amountCents = (int) round(((float) $request->input('amount_dollars')) * 100);
        if ($amountCents < self::DONATION_MIN_CENTS) {
            return back()->withErrors(['amount_dollars' => 'Minimum donation is $2.00.']);
        }

        try {
            $response = self::withoutProxyEnvironment(function () use ($amountCents, $request) {
                $provider = self::makePayPalProvider();
                $provider->getAccessToken();

                return $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('payment.donation.success', ['amount_cents' => $amountCents]),
                    'cancel_url' => route('payment.donation.cancel'),
                ],
                'purchase_units' => [
                    [
                        'description' => 'Support contribution - '.$request->user()->name,
                        'custom_id' => 'user:'.$request->user()->id,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amountCents / 100, 2, '.', ''),
                        ],
                    ],
                ],
            ]);
            });

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            Log::error('PayPal donation createOrder failed', [
                'response' => $response,
                'network_diagnostics' => self::paypalNetworkDiagnostics(),
            ]);

            return back()->with('error', 'Could not start donation checkout. '.self::summarizePayPalResponse((array) $response));
        } catch (\Throwable $e) {
            Log::error('PayPal donation checkout exception', [
                'message' => $e->getMessage(),
                'network_diagnostics' => self::paypalNetworkDiagnostics(),
            ]);
            $msg = $e->getMessage();
            if (self::isTunnelFailureMessage($msg)) {
                return back()->with('error', 'Could not connect to PayPal from this environment. Check outbound network/proxy settings, then try again.');
            }

            return back()->with('error', 'Could not reach PayPal right now. Please try again in a moment.');
        }
    }

    public function donationCancel(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('warning', 'Donation checkout was canceled. You were not charged.');
    }

    public function donationSuccess(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        if (! $token) {
            return redirect()->route('dashboard')->with('error', 'Your donation session was incomplete. Please try again.');
        }

        if (! self::paypalCredentialsConfigured()) {
            return redirect()->route('dashboard')->with('error', 'Donations are temporarily unavailable. Please try again later.');
        }

        try {
            $captureResponse = self::withoutProxyEnvironment(function () use ($token) {
                $provider = self::makePayPalProvider();
                $provider->getAccessToken();

                return $provider->capturePaymentOrder($token);
            });
        } catch (\Throwable $e) {
            Log::error('PayPal donation capture exception', ['message' => $e->getMessage()]);

            return redirect()->route('dashboard')->with('error', 'We could not confirm your donation with PayPal.');
        }

        if (! is_array($captureResponse) || ($captureResponse['status'] ?? null) !== 'COMPLETED') {
            Log::error('PayPal donation capture not completed', ['response' => $captureResponse]);

            return redirect()->route('dashboard')->with('error', 'Donation could not be completed. '.self::summarizePayPalResponse((array) $captureResponse));
        }

        $amountCents = self::extractCapturedUsdCents($captureResponse) ?? (int) $request->query('amount_cents', 0);
        if ($amountCents < self::DONATION_MIN_CENTS) {
            $amountCents = self::DONATION_MIN_CENTS;
        }

        $payment = Payment::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'payment_id' => $token,
                'purpose' => 'donation',
            ],
            [
                'amount_cents' => $amountCents,
                'status' => 'completed',
                'edit_id' => null,
            ]
        );
        if ($payment->wasRecentlyCreated) {
            $this->sendDonationEmails($request->user(), $payment);
        }

        return redirect()->route('dashboard')->with('success', 'Thank you for supporting the publishing fund!');
    }

    public function donationWebhook(Request $request)
    {
        $payload = $request->all();
        if (! $this->webhookAuthorized($request, $payload)) {
            return response()->json(['ok' => false], 401);
        }

        $eventType = (string) ($payload['event_type'] ?? '');
        $resource = is_array($payload['resource'] ?? null) ? $payload['resource'] : [];
        if (! in_array($eventType, ['PAYMENT.CAPTURE.COMPLETED', 'CHECKOUT.ORDER.APPROVED', 'CHECKOUT.ORDER.COMPLETED'], true)) {
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        $orderId = (string) ($resource['supplementary_data']['related_ids']['order_id'] ?? $resource['id'] ?? '');
        $amountValue = $resource['amount']['value'] ?? $resource['purchase_units'][0]['amount']['value'] ?? null;
        $amountCents = is_numeric($amountValue) ? max(0, (int) round(((float) $amountValue) * 100)) : self::DONATION_MIN_CENTS;
        $customId = (string) ($resource['custom_id'] ?? $resource['purchase_units'][0]['custom_id'] ?? '');
        $userId = self::extractUserIdFromCustomId($customId);
        if (! $orderId || ! $userId || ! User::query()->whereKey($userId)->exists()) {
            return response()->json(['ok' => false, 'message' => 'missing required fields'], 422);
        }

        $payment = Payment::firstOrCreate(
            [
                'user_id' => $userId,
                'payment_id' => $orderId,
                'purpose' => 'donation',
            ],
            [
                'amount_cents' => $amountCents,
                'status' => 'completed',
                'edit_id' => null,
            ]
        );
        if ($payment->wasRecentlyCreated) {
            $user = User::query()->find($userId);
            if ($user) {
                $this->sendDonationEmails($user, $payment);
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Prefer PayPal signature verification when webhook id is configured.
     * Fallback for local/dev: shared header token.
     *
     * @param  array<string, mixed>  $payload
     */
    private function webhookAuthorized(Request $request, array $payload): bool
    {
        $webhookId = trim((string) env('PAYPAL_WEBHOOK_ID', ''));
        if ($webhookId !== '') {
            return $this->verifyPayPalWebhookSignature($request, $payload, $webhookId);
        }

        $token = (string) env('PAYPAL_WEBHOOK_TOKEN', '');
        if ($token === '') {
            return false;
        }

        $incoming = (string) $request->header('X-Webhook-Token', '');

        return hash_equals($token, $incoming);
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

    /**
     * @param  array<string, mixed>  $captureResponse
     */
    private static function extractCapturedUsdCents(array $captureResponse): ?int
    {
        $value = $captureResponse['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? null;
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        return max(0, (int) round(((float) $value) * 100));
    }

    private static function isTunnelFailureMessage(string $message): bool
    {
        return str_contains($message, 'CONNECT tunnel failed')
            || str_contains($message, 'cURL error 56')
            || (str_contains($message, 'response 403') && str_contains($message, 'paypal.com'));
    }

    /**
     * Safe, non-secret network diagnostics for troubleshooting proxy/tunnel errors.
     *
     * @return array<string, mixed>
     */
    private static function paypalNetworkDiagnostics(): array
    {
        $envProxy = [];
        foreach (['HTTP_PROXY', 'HTTPS_PROXY', 'NO_PROXY', 'http_proxy', 'https_proxy', 'no_proxy'] as $k) {
            $v = getenv($k);
            if ($v !== false && $v !== '') {
                $envProxy[$k] = $v;
            }
        }

        $sandboxHost = parse_url('https://api-m.sandbox.paypal.com/v2/checkout/orders', PHP_URL_HOST);
        $resolved = $sandboxHost ? gethostbyname($sandboxHost) : null;

        return [
            'php_version' => PHP_VERSION,
            'curl_version' => function_exists('curl_version') ? (curl_version()['version'] ?? null) : null,
            'openssl_version' => \defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : null,
            'paypal_mode' => config('paypal.mode'),
            'paypal_target_host' => $sandboxHost,
            'paypal_target_ip' => $resolved && $resolved !== $sandboxHost ? $resolved : null,
            'proxy_env_present' => array_keys($envProxy),
        ];
    }

    private static function makePayPalProvider(): PayPalClient
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        $mode = strtolower((string) config('paypal.mode', 'sandbox'));
        $apiHost = $mode === 'live' ? 'api-m.paypal.com' : 'api-m.sandbox.paypal.com';
        $forceApiIp = trim((string) config('paypal.force_api_ip', ''));
        $disableProxyEnv = (bool) config('paypal.disable_proxy_env', false);
        $curlOptions = [];
        if ($disableProxyEnv) {
            if (\defined('CURLOPT_PROXY')) {
                $curlOptions[\constant('CURLOPT_PROXY')] = '';
            }
            if (\defined('CURLOPT_NOPROXY')) {
                $curlOptions[\constant('CURLOPT_NOPROXY')] = '*';
            }
            if (\defined('CURLOPT_PROXYTYPE')) {
                $curlOptions[\constant('CURLOPT_PROXYTYPE')] = 0;
            }
        }
        if ($forceApiIp !== '' && \defined('CURLOPT_RESOLVE')) {
            $curlOptions[\constant('CURLOPT_RESOLVE')] = ["{$apiHost}:443:{$forceApiIp}"];
        }

        if ($curlOptions !== []) {
            $provider->setClient(new GuzzleClient([
                'curl' => $curlOptions,
            ]));
        }

        return $provider;
    }

    /**
     * Optionally run PayPal SDK calls with proxy env disabled.
     */
    private static function withoutProxyEnvironment(callable $callback): mixed
    {
        if (! (bool) config('paypal.disable_proxy_env', false)) {
            return $callback();
        }

        $keys = ['HTTP_PROXY', 'HTTPS_PROXY', 'NO_PROXY', 'http_proxy', 'https_proxy', 'no_proxy'];
        $original = [];
        $canPutenv = \function_exists('putenv');
        $canGetenv = \function_exists('getenv');
        foreach ($keys as $key) {
            $value = $canGetenv ? \getenv($key) : ($_ENV[$key] ?? $_SERVER[$key] ?? false);
            $original[$key] = $value === false ? null : $value;
            if ($canPutenv) {
                \putenv($key);
            }
            $_ENV[$key] = '';
            $_SERVER[$key] = '';
        }

        try {
            return $callback();
        } finally {
            foreach ($keys as $key) {
                $value = $original[$key];
                if ($value === null) {
                    if ($canPutenv) {
                        \putenv($key);
                    }
                    unset($_ENV[$key], $_SERVER[$key]);
                } else {
                    if ($canPutenv) {
                        \putenv($key.'='.$value);
                    }
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    /**
     * @return array<int, int>
     */
    private static function decodePendingIds(string $value): array
    {
        $parts = array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== '');
        if ($parts === []) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $parts)));
    }

    private static function extractUserIdFromCustomId(string $customId): ?int
    {
        if (! str_starts_with($customId, 'user:')) {
            return null;
        }
        $id = (int) substr($customId, 5);

        return $id > 0 ? $id : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function verifyPayPalWebhookSignature(Request $request, array $payload, string $webhookId): bool
    {
        $transmissionId = (string) $request->header('PAYPAL-TRANSMISSION-ID', '');
        $transmissionTime = (string) $request->header('PAYPAL-TRANSMISSION-TIME', '');
        $transmissionSig = (string) $request->header('PAYPAL-TRANSMISSION-SIG', '');
        $certUrl = (string) $request->header('PAYPAL-CERT-URL', '');
        $authAlgo = (string) $request->header('PAYPAL-AUTH-ALGO', '');
        if ($transmissionId === '' || $transmissionTime === '' || $transmissionSig === '' || $certUrl === '' || $authAlgo === '') {
            return false;
        }

        $mode = strtolower((string) config('paypal.mode', 'sandbox'));
        $baseUri = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $apiHost = $mode === 'live' ? 'api-m.paypal.com' : 'api-m.sandbox.paypal.com';
        $group = $mode === 'live' ? 'live' : 'sandbox';
        $clientId = trim((string) config("paypal.{$group}.client_id", ''));
        $clientSecret = trim((string) config("paypal.{$group}.client_secret", ''));
        if ($clientId === '' || $clientSecret === '') {
            return false;
        }

        $forceApiIp = trim((string) config('paypal.force_api_ip', ''));
        $disableProxyEnv = (bool) config('paypal.disable_proxy_env', false);
        $curlOptions = [];
        if ($disableProxyEnv) {
            if (\defined('CURLOPT_PROXY')) {
                $curlOptions[\constant('CURLOPT_PROXY')] = '';
            }
            if (\defined('CURLOPT_NOPROXY')) {
                $curlOptions[\constant('CURLOPT_NOPROXY')] = '*';
            }
            if (\defined('CURLOPT_PROXYTYPE')) {
                $curlOptions[\constant('CURLOPT_PROXYTYPE')] = 0;
            }
        }
        if ($forceApiIp !== '' && \defined('CURLOPT_RESOLVE')) {
            $curlOptions[\constant('CURLOPT_RESOLVE')] = ["{$apiHost}:443:{$forceApiIp}"];
        }

        try {
            return (bool) self::withoutProxyEnvironment(function () use ($baseUri, $clientId, $clientSecret, $curlOptions, $transmissionId, $transmissionTime, $transmissionSig, $certUrl, $authAlgo, $webhookId, $payload) {
                $client = new GuzzleClient([
                    'base_uri' => $baseUri,
                    'timeout' => 20,
                    'curl' => $curlOptions,
                ]);

                $tokenResponse = $client->post('/v1/oauth2/token', [
                    'auth' => [$clientId, $clientSecret],
                    'form_params' => ['grant_type' => 'client_credentials'],
                    'headers' => ['Accept' => 'application/json'],
                ]);
                $tokenData = json_decode((string) $tokenResponse->getBody(), true);
                $accessToken = is_array($tokenData) ? (string) ($tokenData['access_token'] ?? '') : '';
                if ($accessToken === '') {
                    return false;
                }

                $verifyResponse = $client->post('/v1/notifications/verify-webhook-signature', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$accessToken,
                    ],
                    'json' => [
                        'transmission_id' => $transmissionId,
                        'transmission_time' => $transmissionTime,
                        'cert_url' => $certUrl,
                        'auth_algo' => $authAlgo,
                        'transmission_sig' => $transmissionSig,
                        'webhook_id' => $webhookId,
                        'webhook_event' => $payload,
                    ],
                ]);

                $verifyData = json_decode((string) $verifyResponse->getBody(), true);

                return strtoupper((string) ($verifyData['verification_status'] ?? '')) === 'SUCCESS';
            });
        } catch (\Throwable $e) {
            Log::warning('PayPal webhook signature verification failed', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function sendDonationEmails(User $user, Payment $payment): void
    {
        $amount = '$'.number_format(((int) $payment->amount_cents) / 100, 2);
        $fromAddress = config('mail.from.address');
        $fromName = 'WhatsMyBookName';

        try {
            Mail::raw(
                "Thank you for your donation of {$amount}.\n\nYour support helps fund publication and community features.\n",
                function ($message) use ($user, $fromAddress, $fromName, $amount) {
                    $message
                        ->from($fromAddress, $fromName)
                        ->to($user->email)
                        ->subject("WhatsMyBookName: donation receipt ({$amount})");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('donation receipt email failed', [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        AdminNotifier::notify(
            'WhatsMyBookName: new donation',
            "Donation received from {$user->name} ({$user->email})\nAmount: {$amount}\nPayment id: {$payment->payment_id}\n\nReview: ".url('/admin/donations')
        );
    }

    private function throttleCheckoutAttempts(Request $request): ?RedirectResponse
    {
        $key = 'payment-checkout:'.$request->user()->id;
        if (RateLimiter::tooManyAttempts($key, 25)) {
            $s = RateLimiter::availableIn($key);

            return back()->with('error', 'Too many checkout attempts. Please wait '.$s.' seconds before trying again.');
        }
        RateLimiter::hit($key, 60);

        return null;
    }
}
