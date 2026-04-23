<?php

return [
    'cover_image_max_kb' => (int) env('BLOG_COVER_IMAGE_MAX_KB', 10240),

    'posts' => [
        [
            'slug' => 'welcome-to-the-liquid-manuscript',
            'title' => 'WhatsMyBookName is Live: Join the Global Collaborative Fiction Experiment',
            'category' => 'Launch',
            'author' => 'Moshe Kagan, Founder',
            'published_at' => '2026-04-22',
            'featured' => true,
            'cover_image_path' => 'blog-assets/hero_banner.png',
            'cover_emoji' => '📖',
            'excerpt' => 'The manuscript is now live, collaborative, and open to edits. Readers can help shape Peter Trull and earn recognition on the final published book.',
            'content' => [
                'Today marks a major milestone in the world of digital storytelling. We are officially launching WhatsMyBookName.com to the world, starting with our debut on Product Hunt.',
                'This is not just another reading platform. It is a collaborative experiment where the manuscript is "liquid." You do not just read the story of Peter Trull, you help shape it. Every accepted edit becomes a permanent part of the novel, and the most impactful contributors can see their names on the final cover.',
                'The problem with traditional publishing is that the relationship between author and reader is static: you read, you finish, and that is it. We wanted to create a space where the reader is an active participant and the story evolves with the community.',
                'At WhatsMyBookName, the manuscript is never truly final until the community decides it is. You can read live chapters, submit your own edits, and vote on which version of the story survives.',
                'Over the next 90 days we will release new chapters, announce Recognition Ladder leaders, and dive deeper into the mystery. This is the beginning of a new era of interactive fiction.',
            ],
        ],
        [
            'slug' => 'inside-the-recognition-ladder',
            'title' => 'The Recognition Ladder: How to Earn Your Name on the Final Cover',
            'category' => 'Community',
            'author' => 'Moshe Kagan, Founder',
            'published_at' => '2026-04-24',
            'featured' => false,
            'cover_image_path' => 'blog-assets/twitter_post_2.png',
            'cover_emoji' => '🏆',
            'excerpt' => 'A breakdown of how accepted edits translate into points, leaderboard momentum, and final-book recognition.',
            'content' => [
                'At the heart of WhatsMyBookName is a gamified contribution system we call the Recognition Ladder. The people who shape the story should be recognized for their impact.',
                'Every submitted edit is reviewed by our editorial team. If your edit is accepted, you earn points: 2 points for a full replacement that significantly improves the text, 1 point for a partial replacement or minor correction, and 0 points if the edit is rejected.',
                'Your acceptance rate and consistency push you up the global leaderboard over time. Precision compounds.',
                'Final rewards are tied to placement: #3 can name a character, #2 helps name the book, and #1 receives final cover credit on the published edition (subject to production approval).',
                'Additional rewards include signed first-print copies for the Top 10 and permanent Hall of Fame listing for the Top 50 contributors.',
            ],
        ],
        [
            'slug' => 'who-is-peter-trull',
            'title' => 'Who is Peter Trull? Inside the High-Stakes World of Our First Interactive Mystery',
            'category' => 'The Story',
            'author' => 'Moshe Kagan, Founder',
            'published_at' => '2026-04-23',
            'featured' => false,
            'cover_image_path' => 'blog-assets/twitter_post_4.png',
            'cover_emoji' => '🕵️',
            'excerpt' => 'Meet the Navy intelligence officer at the center of our first interactive mystery, where readers decide which path survives.',
            'content' => [
                'Our first interactive mystery, Peter Trull: Solitary Detective, is a high-stakes journey into the mind of a damaged officer facing an unseen threat.',
                'Peter Trull is a Navy intelligence officer struggling with CPTSD. He has spent his life analyzing danger, but now the greatest threat may be his own mind.',
                'This is not a traditional mystery with a locked ending. At critical split points, the community decides which path survives.',
                'To vote on Peter Trull\'s fate, readers need unused vote credits. Those credits are earned by completing accepted contributions in The Book With No Name. One accepted edit equals one vote credit.',
                'The choices made by the community determine whether Peter uncovers the truth or falls to the ghosts that haunt him.',
            ],
        ],
        [
            'slug' => 'single-word-edits-that-win',
            'title' => 'Single-word edits that actually win',
            'category' => 'Craft',
            'author' => 'Moshe Kagan, Founder',
            'published_at' => '2026-04-20',
            'featured' => false,
            'cover_emoji' => '🔎',
            'excerpt' => 'A practical look at why certain minimal edits survive moderation and improve clarity without flattening voice.',
            'content' => [
                'Most winning edits are not dramatic rewrites. They are precise word substitutions that preserve tone while improving clarity, pacing, or specificity.',
                'When you submit, aim for intent preservation first: keep character voice and scene logic intact, then tighten. Moderation rewards precision more than novelty.',
            ],
        ],
    ],
    'quick_updates' => [
        [
            'title' => 'Next chapter window opens this week',
            'meta' => 'Scheduling',
            'summary' => 'The next editing window is being finalized now. Keep an eye on the Chapters page for the open/close timer.',
        ],
        [
            'title' => 'Leaderboard tie-break wording refreshed',
            'meta' => 'Policy',
            'summary' => 'We clarified tie-break language in visible copy so placement outcomes are easier to understand.',
        ],
        [
            'title' => 'More contributor profiles now public',
            'meta' => 'Community',
            'summary' => 'Contributors are enabling shareable public profiles and linking accepted edits from their profile history.',
        ],
    ],
];
