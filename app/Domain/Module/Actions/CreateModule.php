<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Models\Module;

class CreateModule
{
    public function handle(CourseSection $section, array $data): Module
    {
        $orderNo = isset($data['order_no'])
            ? (int) $data['order_no']
            : $section->modules()->count() + 1;

        return Module::create([
            'course_section_id' => $section->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'order_no' => $orderNo,
        ]);
    }
}
