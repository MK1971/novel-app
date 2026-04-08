<?php

namespace Tests\Unit;

use App\Models\Achievement;
use PHPUnit\Framework\TestCase;

class AchievementRequirementLabelTest extends TestCase
{
    public function test_requirement_label_formats_known_types(): void
    {
        $a = new Achievement([
            'requirement_type' => 'points_earned',
            'requirement_value' => 10,
        ]);
        $this->assertStringContainsString('10', $a->requirementLabel());
        $this->assertStringContainsString('point', $a->requirementLabel());
    }
}
