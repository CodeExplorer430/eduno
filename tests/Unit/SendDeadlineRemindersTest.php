<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Console\Commands\SendDeadlineReminders;
use PHPUnit\Framework\TestCase;

class SendDeadlineRemindersTest extends TestCase
{
    private SendDeadlineReminders $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new SendDeadlineReminders();
    }

    public function test_command_has_correct_signature(): void
    {
        $this->assertSame('schedule:deadline-reminders', $this->command->getName());
    }

    public function test_command_has_non_empty_description(): void
    {
        $this->assertNotEmpty($this->command->getDescription());
    }
}
