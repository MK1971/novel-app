<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_page_returns_ok(): void
    {
        $this->get(route('privacy'))->assertOk();
    }

    public function test_terms_page_returns_ok(): void
    {
        $this->get(route('terms'))->assertOk();
    }

    public function test_about_page_returns_ok(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Privacy Policy', false)
            ->assertSee('Terms of Service', false);
    }

    public function test_public_legal_pages_include_meta_description(): void
    {
        foreach ([
            route('about'),
            route('privacy'),
            route('terms'),
            route('prizes'),
            route('legal.index'),
            route('legal.refunds'),
            route('legal.community'),
            route('legal.cookies'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('<meta name="description"', false);
        }
    }

    public function test_legal_hub_and_subpages_return_ok(): void
    {
        $this->get(route('legal.index'))->assertOk()->assertSee('Legal', false);
        $this->get(route('legal.refunds'))->assertOk()->assertSee('Refunds', false);
        $this->get(route('legal.community'))->assertOk()->assertSee('Community guidelines', false);
        $this->get(route('legal.cookies'))->assertOk()->assertSee('Cookie policy', false);
    }
}
