<?php

declare(strict_types=1);

use App\Domain\Course\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('has expected fillable attributes', function (): void {
    $model = new Course();
    expect($model->getFillable())->toContain('code', 'title', 'status');
});

it('uses CourseSection relationship', function (): void {
    $course = new Course();
    expect($course->sections())->toBeInstanceOf(HasMany::class);
});

it('uses creator relationship', function (): void {
    $course = new Course();
    expect($course->creator())->toBeInstanceOf(BelongsTo::class);
});
