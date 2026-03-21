<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Module;

class CreateModule
{
    /**
     * @param  array{title: string, description: string|null, order_no: int, published: bool}  $data
     */
    public function execute(int $courseSectionId, array $data): Module
    {
        return Module::create([
            'course_section_id' => $courseSectionId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'order_no' => $data['order_no'],
            'published_at' => $data['published'] ? now() : null,
        ]);
    }
}
