<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin reports page includes charts prop with all four keys', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Admin/Reports/Index')
        ->has('charts.submission_trend')
        ->has('charts.grade_distribution')
        ->has('charts.late_rate_by_course')
        ->has('charts.status_breakdown')
    );
});

test('submission_trend has 8 weekly labels and matching on-time and late arrays', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $trend = $page->toArray()['props']['charts']['submission_trend'];

        expect($trend['labels'])->toHaveCount(8);
        expect($trend['onTime'])->toHaveCount(8);
        expect($trend['late'])->toHaveCount(8);

        foreach ($trend['onTime'] as $v) {
            expect($v)->toBeInt()->toBeGreaterThanOrEqual(0);
        }
        foreach ($trend['late'] as $v) {
            expect($v)->toBeInt()->toBeGreaterThanOrEqual(0);
        }
    });
});

test('grade_distribution has exactly 6 score band labels', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $dist = $page->toArray()['props']['charts']['grade_distribution'];

        expect($dist['labels'])->toHaveCount(6);
        expect($dist['counts'])->toHaveCount(6);
        expect($dist['labels'])->toBe(['0–49', '50–59', '60–69', '70–79', '80–89', '90–100']);
    });
});

test('late_rate_by_course rates are percentages between 0 and 100', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $lateRate = $page->toArray()['props']['charts']['late_rate_by_course'];

        expect(count($lateRate['labels']))->toBe(count($lateRate['rates']));

        foreach ($lateRate['rates'] as $rate) {
            expect($rate)->toBeFloat()->toBeGreaterThanOrEqual(0.0)->toBeLessThanOrEqual(100.0);
        }
    });
});

test('status_breakdown labels and counts arrays have matching lengths', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $status = $page->toArray()['props']['charts']['status_breakdown'];

        expect(count($status['labels']))->toBe(count($status['counts']));
    });
});
