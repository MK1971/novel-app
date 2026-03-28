<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_returns_ok(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_welcome_includes_accessibility_and_structure_markers(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<main id="main-content"', $html);
        $this->assertStringContainsString('tabindex="-1"', $html);
        $this->assertStringContainsString('id="landing-root"', $html);
        $this->assertStringContainsString('class="skip-to-main"', $html);
        $this->assertStringContainsString('<button type="button" class="skip-to-main"', $html);
        $this->assertStringContainsString('skipToMainContent', $html);
        $this->assertStringContainsString('mobileNavOpen', $html);
        $this->assertStringContainsString('id="landing-mobile-menu"', $html);
        $this->assertStringContainsString('id="landing-stats-heading"', $html);
        $this->assertStringContainsString('class="sr-only"', $html);
        $this->assertStringContainsString('aria-labelledby="landing-stats-heading"', $html);
        $this->assertStringContainsString('id="landing-hero-cta-subline"', $html);
        $this->assertStringContainsString('id="landing-social-proof"', $html);
        $this->assertStringContainsString('id="landing-stats-footnote"', $html);
    }

    public function test_welcome_includes_reduced_motion_and_focus_styles(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('prefers-reduced-motion: reduce', $html);
        $this->assertStringContainsString('.hero-ping-dot', $html);
        $this->assertStringContainsString('#landing-root nav :is(a, button):focus', $html);
    }

    public function test_welcome_blade_contains_expected_hooks(): void
    {
        $src = file_get_contents(resource_path('views/welcome.blade.php'));

        $this->assertStringContainsString('landing-motion-card', $src);
        $this->assertStringContainsString('hero-ping-dot', $src);
        $this->assertStringContainsString('hero-foreground', $src);
        $this->assertStringContainsString('max-width: 767px', $src);
        $this->assertStringContainsString('background-attachment: scroll', $src);
        $this->assertMatchesRegularExpression('/min-width:\s*768px[\s\S]*background-attachment:\s*fixed/s', $src);
    }
}
