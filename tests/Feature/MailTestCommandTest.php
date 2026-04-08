<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailTestCommandTest extends TestCase
{
    public function test_mail_test_command_runs_successfully_with_array_mailer(): void
    {
        $this->artisan('mail:test', ['email' => 'qa@example.com'])
            ->assertSuccessful();
    }
}
