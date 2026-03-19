<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadResource
{
    public function handle(
        Lesson $lesson,
        UploadedFile $file,
        string $title,
        ResourceVisibility $visibility
    ): Resource {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        $module = $lesson->module;
        $path = $file->storeAs(
            "resources/{$module->course_section_id}/{$module->id}/{$lesson->id}",
            $filename,
            'private'
        );

        return DB::transaction(function () use ($lesson, $title, $path, $file, $visibility): Resource {
            return Resource::create([
                'lesson_id' => $lesson->id,
                'title' => $title,
                'file_path' => (string) $path,
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'size_bytes' => $file->getSize(),
                'visibility' => $visibility,
            ]);
        });
    }
}
