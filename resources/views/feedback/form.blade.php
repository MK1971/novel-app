<div class="bg-amber-50 p-8 rounded-2xl border border-amber-100 shadow-inner">
    <h3 class="text-2xl font-bold text-amber-900 mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        Share Your Feedback
    </h3>
    
    <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($chapter_id))
            <input type="hidden" name="chapter_id" value="{{ $chapter_id }}">
            <input type="hidden" name="type" value="chapter">
        @else
            <div>
                <label for="type" class="block text-sm font-bold text-amber-900 mb-2">Feedback Type</label>
                <select name="type" id="type" class="w-full bg-white border-amber-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 text-amber-900">
                    <option value="general">General Feedback</option>
                    <option value="suggestion">Story Suggestion</option>
                    <option value="bug">Report a Bug</option>
                </select>
            </div>
        @endif

        @guest
            <div>
                <label for="email" class="block text-sm font-bold text-amber-900 mb-2">Your Email (Optional)</label>
                <input type="email" name="email" id="email" class="w-full bg-white border-amber-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 text-amber-900" placeholder="Enter your email if you'd like a response">
            </div>
        @endguest

        <div>
            <label for="content" class="block text-sm font-bold text-amber-900 mb-2">Your Message</label>
            <textarea name="content" id="content" rows="4" class="w-full bg-white border-amber-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 text-amber-900" placeholder="What's on your mind?" required></textarea>
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-amber-600 border border-transparent rounded-full font-bold text-white hover:bg-amber-700 transition ease-in-out duration-150 shadow-md">
            Submit Feedback
        </button>
    </form>
</div>
