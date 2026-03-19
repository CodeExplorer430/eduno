<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'instructor1@eduno.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Instructor,
        ]);

        User::factory()->create([
            'name' => 'Jose Reyes',
            'email' => 'instructor2@eduno.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Instructor,
        ]);
    }
}
