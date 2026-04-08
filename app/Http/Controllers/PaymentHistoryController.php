<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentHistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $payments = Payment::query()
            ->where('user_id', $request->user()->id)
            ->with(['edit.chapter', 'vote.chapter'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('profile.payments', compact('payments'));
    }
}
