<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">
                @can('admin') ⚙️ Admin Dashboard @else 📊 Dashboard @endcan
            </h2>
            <p class="text-amber-800/60 font-bold">Welcome, {{ auth()->user()->name }}!</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @can('admin')
                {{-- ADMIN DASHBOARD --}}
                
                {{-- Key Metrics --}}
                <div class="grid md:grid-cols-4 gap-6 mb-12">
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-amber-600 mb-2">{{ \App\Models\User::count() - 1 }}</div>
                        <p class="text-amber-800/60 font-bold">Total Users</p>
                        <p class="text-xs text-amber-800/40 font-bold mt-2">(excluding admin)</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-blue-600 mb-2">{{ \App\Models\Edit::where('status', 'pending')->count() }}</div>
                        <p class="text-blue-800/60 font-bold">Pending Edits</p>
                        <p class="text-xs text-blue-800/40 font-bold mt-2">Awaiting review</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-green-600 mb-2">{{ \App\Models\Edit::whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count() }}</div>
                        <p class="text-green-800/60 font-bold">Accepted Edits</p>
                        <p class="text-xs text-green-800/40 font-bold mt-2">Integrated into novel</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-purple-600 mb-2">{{ \App\Models\Chapter::count() }}</div>
                        <p class="text-purple-800/60 font-bold">Total Chapters</p>
                        <p class="text-xs text-purple-800/40 font-bold mt-2">Across all books</p>
                    </div>
                </div>

                {{-- Pending Edits for Review --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 mb-12">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">📝 Pending Edits for Review</h3>
                    
                    @php
                        $pendingEdits = \App\Models\Edit::where('status', 'pending')
                            ->with('user', 'chapter')
                            ->orderByDesc('created_at')
                            ->limit(10)
                            ->get();
                    @endphp
                    
                    @if($pendingEdits->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingEdits as $edit)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 hover:shadow-lg transition-all">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-extrabold text-amber-900">{{ $edit->user->name }}</span>
                                                <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded">{{ ucfirst($edit->type) }}</span>
                                            </div>
                                            <p class="text-sm text-amber-900/60 font-bold mb-2">{{ $edit->chapter->title }}</p>
                                            <p class="text-xs text-amber-800/60 font-bold">{{ $edit->created_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('admin.edits.index') }}" class="px-4 py-2 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition-colors text-sm flex-shrink-0">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No pending edits to review</p>
                        </div>
                    @endif
                </div>

                {{-- Recent Feedback --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 mb-12">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">💬 Recent Feedback</h3>
                    
                    @php
                        $recentFeedback = \App\Models\Feedback::with('user')
                            ->orderByDesc('created_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recentFeedback->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentFeedback as $feedback)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                                    <div class="flex items-start justify-between gap-4 mb-2 flex-wrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-extrabold text-amber-900">{{ $feedback->user?->name ?? $feedback->email ?? 'Anonymous' }}</span>
                                            <span class="text-xs font-bold text-amber-700/80 uppercase tracking-wider">{{ str_replace('_', ' ', $feedback->type) }}</span>
                                        </div>
                                        <p class="text-xs text-amber-800/60 font-bold">{{ $feedback->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm text-amber-900/80 font-bold">{{ Str::limit($feedback->content, 150) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No feedback received yet</p>
                        </div>
                    @endif
                </div>

                {{-- Top Contributors --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">🏆 Top Contributors</h3>
                    
                    @php
                        $topContributors = \App\Models\User::where('is_admin', false)
                            ->orderByDesc('points')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($topContributors->count() > 0)
                        <div class="space-y-3">
                            @foreach($topContributors as $index => $contributor)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <span class="text-2xl font-extrabold text-amber-600">{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-extrabold text-amber-900">{{ $contributor->name }}</p>
                                            <p class="text-xs text-amber-800/60 font-bold">{{ $contributor->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-extrabold text-amber-600">{{ $contributor->points }}</p>
                                        <p class="text-xs text-amber-800/60 font-bold">points</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No contributors yet</p>
                        </div>
                    @endif
                </div>

            @else
                {{-- USER DASHBOARD --}}
                
                <div class="grid md:grid-cols-4 gap-6 mb-12">
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-amber-600 mb-2">{{ auth()->user()->points }}</div>
                        <p class="text-amber-800/60 font-bold">Your Points</p>
                        <p class="text-xs text-amber-800/40 font-bold mt-2">Up to 2 pts per accepted edit (2 full · 1 partial · 0 rejected)</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-blue-600 mb-2">{{ auth()->user()->edits()->count() + auth()->user()->inlineEdits()->count() }}</div>
                        <p class="text-blue-800/60 font-bold">Your Edits</p>
                        <p class="text-xs text-blue-800/40 font-bold mt-2">Suggestions submitted</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-green-600 mb-2">{{ auth()->user()->edits()->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count() + auth()->user()->inlineEdits()->where('status', 'approved')->count() }}</div>
                        <p class="text-green-800/60 font-bold">Accepted</p>
                        <p class="text-xs text-green-800/40 font-bold mt-2">Edits in the novel</p>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-[2rem] p-8">
                        <div class="text-4xl font-extrabold text-red-600 mb-2">{{ auth()->user()->edits()->where('status', 'rejected')->count() + auth()->user()->inlineEdits()->where('status', 'rejected')->count() }}</div>
                        <p class="text-red-800/60 font-bold">Rejected</p>
                        <p class="text-xs text-red-800/40 font-bold mt-2">Suggestions declined</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8">
                        <h3 class="text-2xl font-extrabold text-amber-900 mb-6">📚 Quick Links</h3>
                        <div class="grid gap-4">
                            <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">📖 Read Chapters</p>
                                <p class="text-sm text-amber-800/60 font-bold">Explore and edit the story</p>
                            </a>
                            <a href="{{ route('vote.index') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">🗳️ Peter Trull Solitary Detective · Vote</p>
                                @if($canVote ?? false)
                                    <p class="text-sm text-amber-800/60 font-bold">Compare versions and cast your vote</p>
                                @else
                                    <p class="text-sm text-amber-800/60 font-bold">You need at least one <span class="text-amber-900">unused paid edit</span> (completed $2 checkout) for a vote credit. Each payment adds one vote for Peter Trull Solitary Detective. You can still open the page without credits.</p>
                                @endif
                            </a>
                            <a href="{{ route('leaderboard') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">🏆 Leaderboard</p>
                                <p class="text-sm text-amber-800/60 font-bold">See top contributors</p>
                            </a>
                            <a href="{{ route('analytics.index') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                                <p class="font-extrabold text-amber-900 mb-1"><span aria-hidden="true">📊</span> Community insights</p>
                                <p class="text-sm text-amber-800/60 font-bold">Stats, voting trends, and recent activity — plus link to the full feed</p>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8">
                        <h3 class="text-2xl font-extrabold text-amber-900 mb-6">🎖️ Your Achievements</h3>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($achievements as $achievement)
                                @php
                                    $isUnlocked = in_array($achievement->id, $userAchievements);
                                @endphp
                                <a href="{{ route('achievements.show', $achievement) }}" class="flex flex-col items-center text-center p-4 rounded-2xl border {{ $isUnlocked ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-gray-100 opacity-40' }} hover:shadow-md transition-all">
                                    <div class="text-3xl mb-2" aria-hidden="true">{{ $achievement->icon_emoji }}</div>
                                    <p class="text-xs font-extrabold text-amber-900 leading-tight">{{ $achievement->name }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
