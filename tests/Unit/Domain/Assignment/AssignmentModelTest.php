<?php

declare(strict_types=1);

use App\Domain\Assignment\Models\Assignment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('has expected fillable attributes', function (): void {
    $model = new Assignment;
    expect($model->getFillable())->toContain(
        'course_section_id',
        'title',
        'due_at',
        'max_score',
        'allow_resubmission',
    );
});

it('uses courseSection relationship', function (): void {
    $assignment = new Assignment;
    expect($assignment->courseSection())->toBeInstanceOf(BelongsTo::class);
});

it('uses submissions relationship', function (): void {
    $assignment = new Assignment;
    expect($assignment->submissions())->toBeInstanceOf(HasMany::class);
});
