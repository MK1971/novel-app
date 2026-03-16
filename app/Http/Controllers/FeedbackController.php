<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::latest()->limit(10)->get();
        return view('feedback.index', compact('feedbacks'));
    }

    public function adminIndex()
    {
        $feedback = Feedback::with(['user', 'chapter'])->latest()->paginate(20);
        return view('admin.feedback.index', compact('feedback'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'required|string|in:general,chapter,bug,suggestion',
            'chapter_id' => 'nullable|exists:chapters,id',
            'email' => 'nullable|email|max:255',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'chapter_id' => $request->chapter_id,
            'content' => $request->content,
            'type' => $request->type,
            'email' => Auth::check() ? Auth::user()->email : $request->email,
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
