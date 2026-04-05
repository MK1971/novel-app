<?php

namespace App\Support;

/**
 * URL slugs disallowed for /people/{slug} (first-path segment conflicts & common abuse).
 */
final class ReservedPublicProfileSlugs
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            'admin', 'api', 'www', 'mail', 'ftp', 'cdn', 'assets', 'static',
            'dashboard', 'profile', 'login', 'register', 'logout', 'password',
            'forgot-password', 'reset-password', 'verify-email', 'legal', 'privacy', 'terms',
            'about', 'chapters', 'chapter', 'vote', 'leaderboard', 'feedback', 'notifications',
            'payment', 'payments', 'analytics', 'achievements', 'archive', 'feed', 'people',
            'u', 'user', 'users', 'settings', 'edit', 'edits', 'storage', 'build', 'sanctum',
            'oauth', 'auth', 'up', 'health', 'dev', 'download', 'support', 'help', 'status',
            'blog', 'news', 'shop', 'cart', 'checkout', 'account', 'me', 'root', 'null',
            'undefined', 'test', 'staging', 'app', 'webhook', 'webhooks', 'inline-edit',
        ];
    }
}
