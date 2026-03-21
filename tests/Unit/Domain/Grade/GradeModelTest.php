<?php

declare(strict_types=1);

use App\Domain\Grade\Models\Grade;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('has expected fillable attributes', function (): void {
    $model = new Grade();
    expect($model->getFillable())->toContain(
        'submission_id',
        'graded_by',
        'score',
        'feedback',
    );
});

it('uses submission relationship', function (): void {
    $grade = new Grade();
    expect($grade->submission())->toBeInstanceOf(BelongsTo::class);
});

it('uses grader relationship', function (): void {
    $grade = new Grade();
    expect($grade->grader())->toBeInstanceOf(BelongsTo::class);
});
