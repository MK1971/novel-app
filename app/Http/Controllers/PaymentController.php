<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'type' => 'required|in:writing,phrase',
            'edited_text' => 'required|string',
        ]);

        $chapter = \App\Models\Chapter::findOrFail($request->chapter_id);

        $edit = \App\Models\Edit::create([
            'user_id' => $request->user()->id,
            'chapter_id' => $chapter->id,
            'type' => $request->type,
            'original_text' => $chapter->content,
            'edited_text' => $request->edited_text,
            'status' => 'pending_payment',
        ]);

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
                    'cancel_url' => route('chapters.show', $chapterId),
                ],
                'purchase_units' => [
                    [
                        'description' => 'Suggest Edit - ' . $chapter->title . ' - The Book With No Name',
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

            $edit->update(['status' => 'cancelled']);
            $errorMsg = $response['message'] ?? $response['error'] ?? json_encode($response);
            \Log::error('PayPal createOrder failed', ['response' => $response]);
            return back()->with('error', 'Could not create PayPal checkout: ' . (is_string($errorMsg) ? $errorMsg : json_encode($errorMsg)));
        } catch (\Throwable $e) {
            $edit->update(['status' => 'cancelled']);
            \Log::error('PayPal checkout exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'PayPal error: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $token = $request->query('token');
        $chapterId = $request->query('chapter_id');
        $editId = $request->query('edit_id');
        if (!$token || !$chapterId || !$editId) {
            return redirect()->route('chapters.index')->with('error', 'Invalid payment session.');
        }

        $edit = \App\Models\Edit::find($editId);
        if (!$edit || $edit->status !== 'pending_payment' || $edit->user_id !== $request->user()->id) {
            return redirect()->route('chapters.index')->with('error', 'Invalid edit session.');
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $captureResponse = $provider->capturePaymentOrder($token);

        if (isset($captureResponse['status']) && $captureResponse['status'] === 'COMPLETED') {
            Payment::create([
                'user_id' => $request->user()->id,
                'amount_cents' => 200,
                'payment_id' => $token,
                'status' => 'completed',
                'edit_id' => $editId,
            ]);

            $edit->update(['status' => 'pending']);

            return redirect()->route('chapters.show', $chapterId)
                ->with('success', 'Thank you for your submission! We will review your edit.');
        }

        return redirect()->route('chapters.show', $chapterId)->with('error', 'Payment could not be completed.');
    }
}
