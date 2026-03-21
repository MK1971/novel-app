<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edit;
use App\Models\Achievement;
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
        
        // Check and unlock achievements
        $this->checkAndUnlockAchievements($edit->user);

        return back()->with('success', 'Edit approved.');
    }

    public function reject(Edit $edit)
    {
        Gate::authorize('admin');
        $edit->update(['status' => 'rejected']);
        return back()->with('success', 'Edit rejected.');
    }
    
    private function checkAndUnlockAchievements($user)
    {
        $achievements = Achievement::all();
        
        foreach ($achievements as $achievement) {
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }
            
            $hasAchievement = false;
            
            switch ($achievement->requirement_type) {
                case 'edits_accepted':
                    $acceptedEdits = $user->edits()->whereIn('status', ['accepted', 'accepted_partial'])->count();
                    $hasAchievement = $acceptedEdits >= $achievement->requirement_value;
                    break;
                    
                case 'votes_cast':
                    $votesCast = $user->votes()->count();
                    $hasAchievement = $votesCast >= $achievement->requirement_value;
                    break;
                    
                case 'points_earned':
                    $hasAchievement = $user->points >= $achievement->requirement_value;
                    break;
                    
                case 'chapters_read':
                    $chaptersRead = $user->readingProgress()->where('completed', true)->count();
                    $hasAchievement = $chaptersRead >= $achievement->requirement_value;
                    break;
            }
            
            if ($hasAchievement) {
                $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
            }
        }
    }
}
