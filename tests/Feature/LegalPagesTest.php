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
}
