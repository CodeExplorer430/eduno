<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Lesson;
use App\Enums\LessonType;

class UpdateLesson
{
    public function handle(Lesson $lesson, array $data): Lesson
    {
        $lesson->title = $data['title'];
        $lesson->content = $data['content'] ?? null;
        $lesson->type = LessonType::from($data['type']);

        if (isset($data['order_no'])) {
            $lesson->order_no = (int) $data['order_no'];
        }

        $lesson->save();

        return $lesson;
    }
}
