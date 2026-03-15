<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\LessonType;

class CreateLesson
{
    public function handle(Module $module, array $data): Lesson
    {
        $orderNo = isset($data['order_no'])
            ? (int) $data['order_no']
            : $module->lessons()->count() + 1;

        return Lesson::create([
            'module_id' => $module->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'type' => LessonType::from($data['type']),
            'order_no' => $orderNo,
        ]);
    }
}
