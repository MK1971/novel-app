<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edit;
use Illuminate\Http\Request;

class EditApprovalController extends Controller
{
    public function index()
    {
        $edits = Edit::with(['user', 'chapter'])->where('status', 'pending')->orderBy('created_at')->get();
        return view('admin.edits.index', compact('edits'));
    }

    public function approve(Edit $edit, Request $request)
    {
        $status = $request->input('status');
        $points = $status === 'accepted_full' ? 2 : 1;

        $edit->update(['status' => $status, 'points_awarded' => $points]);
        $edit->user->increment('points', $points);

        return back()->with('success', 'Edit approved.');
    }

    public function reject(Edit $edit)
    {
        $edit->update(['status' => 'rejected']);
        return back()->with('success', 'Edit rejected.');
    }
}
