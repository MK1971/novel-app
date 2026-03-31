<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsInsightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_page_loads_with_insight_sections(): void
    {
        $this->get(route('analytics.index'))
            ->assertOk()
            ->assertSee('Community insights', false)
            ->assertSee('Manuscript contributions by chapter', false)
            ->assertSee('Peter Trull Solitary Detective — voting trends', false);
    }
}
