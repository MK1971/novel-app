<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Support\ReleaseNotificationService;
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
        $isWaitlist = $request->input('type') === 'waitlist';

        $request->validate([
            'content' => ($isWaitlist ? 'nullable' : 'required').'|string|max:1000',
            'type' => 'required|string|in:general,chapter,bug,suggestion,site_suggestion,accessibility,account,payment,content_issue,waitlist',
            'chapter_id' => 'nullable|exists:chapters,id',
            'email' => 'nullable|email|max:255',
        ]);

        if ($isWaitlist && ! Auth::check() && ! filled($request->email)) {
            return redirect()->to(route('home').'#landing-updates-signup')->withErrors([
                'email' => 'Email is required to join updates.',
            ])->withInput();
        }

        $content = trim((string) $request->input('content', ''));
        if ($isWaitlist && $content === '') {
            $content = 'Landing updates waitlist signup';
        }

        Feedback::create([
            'user_id' => Auth::id(),
            'chapter_id' => $request->chapter_id,
            'content' => $content,
            'type' => $request->type,
            'email' => Auth::check() ? Auth::user()->email : $request->email,
        ]);

        if ($isWaitlist) {
            $recipientEmail = Auth::check() ? Auth::user()->email : (string) $request->input('email', '');
            app(ReleaseNotificationService::class)->sendWaitlistConfirmation($recipientEmail);

            return redirect()->to(route('home').'#landing-updates-signup')
                ->with('success', 'You are on the updates list. We will email you when chapters open.');
        }

        return back()->with('success', 'Thank you for your feedback!');
    }
}
