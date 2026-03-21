<?php

declare(strict_types=1);

use App\Domain\Content\Models\Module;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('has expected fillable attributes', function (): void {
    $model = new Module();
    expect($model->getFillable())->toContain('course_section_id', 'title', 'order_no');
});

it('uses courseSection relationship', function (): void {
    $module = new Module();
    expect($module->courseSection())->toBeInstanceOf(BelongsTo::class);
});

it('uses lessons relationship', function (): void {
    $module = new Module();
    expect($module->lessons())->toBeInstanceOf(HasMany::class);
});
