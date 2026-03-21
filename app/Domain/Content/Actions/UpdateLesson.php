<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Lesson;

class UpdateLesson
{
    /**
     * @param  array{title: string, type: string, content: string|null, order_no: int, published: bool}  $data
     */
    public function execute(Lesson $lesson, array $data): Lesson
    {
        $published = $data['published'];

        $lesson->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'content' => $data['content'] ?? null,
            'order_no' => $data['order_no'],
            'published_at' => $published
                ? ($lesson->published_at ?? now())
                : null,
        ]);

        return $lesson;
    }
}
