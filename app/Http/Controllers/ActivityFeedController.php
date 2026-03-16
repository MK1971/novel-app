<?php

namespace App\Http\Controllers;

use App\Models\ActivityFeed;
use Illuminate\Http\Request;

class ActivityFeedController extends Controller
{
    public function index()
    {
        $activities = ActivityFeed::with('user', 'chapter')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('activity-feed.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type' => 'required|string',
            'description' => 'required|string',
            'chapter_id' => 'nullable|exists:chapters,id',
            'metadata' => 'nullable|json',
        ]);

        ActivityFeed::create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        return back()->with('success', 'Activity recorded!');
    }
}
