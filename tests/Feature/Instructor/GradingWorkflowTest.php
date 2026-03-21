<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

it('instructor can access their courses list', function () {
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);

    $response = $this->actingAs($instructor)->get(route('instructor.courses.index'));

    $response->assertStatus(200);
});

it('student cannot access instructor courses', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('instructor.courses.index'));

    $response->assertStatus(403);
});
