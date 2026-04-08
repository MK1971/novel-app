<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Response;

class DonationReportController extends Controller
{
    public function index()
    {
        $rows = Payment::query()
            ->with('user:id,name,email')
            ->where('purpose', 'donation')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(300)
            ->get();

        $totalCents = (int) $rows->sum('amount_cents');
        $usesSignatureVerification = trim((string) env('PAYPAL_WEBHOOK_ID', '')) !== '';

        return view('admin.donations.index', [
            'rows' => $rows,
            'totalCents' => $totalCents,
            'usesSignatureVerification' => $usesSignatureVerification,
        ]);
    }

    public function exportCsv(): Response
    {
        $rows = Payment::query()
            ->with('user:id,name,email')
            ->where('purpose', 'donation')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        $csv = "\"user\",\"email\",\"amount_usd\",\"paypal_order_id\",\"created_at\"\n";
        foreach ($rows as $row) {
            $line = [
                (string) ($row->user?->name ?? 'Unknown'),
                (string) ($row->user?->email ?? ''),
                number_format(((int) $row->amount_cents) / 100, 2, '.', ''),
                (string) ($row->payment_id ?? ''),
                (string) $row->created_at,
            ];
            $csv .= implode(',', array_map(fn (string $v) => '"'.str_replace('"', '""', $v).'"', $line))."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="donations-report.csv"',
        ]);
    }
}
