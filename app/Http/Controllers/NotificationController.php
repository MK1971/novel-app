<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderByDesc('created_at')
            ->get();

        $unreadCount = Auth::user()->notifications()
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        $updated = Auth::user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            return back()->with('success', "Marked {$updated} notification(s) as read.");
        }

        return back()->with('success', 'No unread notifications to mark.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        Notification::create($validated);

        return back()->with('success', 'Notification sent');
    }
}
