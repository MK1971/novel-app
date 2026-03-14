<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EditApprovalController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        $edits = Edit::with(['user', 'chapter'])->where('status', 'pending')->orderBy('created_at')->get();
        return view('admin.edits.index', compact('edits'));
    }

    public function approve(Edit $edit, Request $request)
    {
        Gate::authorize('admin');
        $status = $request->input('status');
        $points = $status === 'accepted_full' ? 2 : 1;

        $edit->update(['status' => $status, 'points_awarded' => $points]);
        $edit->user->increment('points', $points);

        return back()->with('success', 'Edit approved.');
    }

    public function reject(Edit $edit)
    {
        Gate::authorize('admin');
        $edit->update(['status' => 'rejected']);
        return back()->with('success', 'Edit rejected.');
    }
}
