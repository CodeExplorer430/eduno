<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default settings
        Setting::set('site_name', 'Eduno', 'string', 'general');
        Setting::set('registration_open', 'true', 'boolean', 'general');
        Setting::set('maintenance_mode', 'false', 'boolean', 'general');
        Setting::set('email_notifications', 'true', 'boolean', 'notifications');
        Setting::set('deadline_reminder_hours', '24', 'integer', 'notifications');
        Setting::set('email_digest', 'false', 'boolean', 'notifications');

        $this->call([
            DemoSeeder::class,
        ]);
    }
}
