<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

it('student can access their courses list', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);

    $response = $this->actingAs($student)->get(route('student.courses.index'));

    $response->assertStatus(200);
});

it('guest is redirected from student courses', function () {
    $response = $this->get(route('student.courses.index'));

    $response->assertRedirect(route('login'));
});
