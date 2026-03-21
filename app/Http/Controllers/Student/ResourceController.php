<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Content\Models\Resource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceController extends Controller
{
    public function download(Request $request, Resource $resource): StreamedResponse
    {
        $resource->load(['lesson.module.courseSection']);

        $enrolled = $request->user()
            ->enrollments()
            ->where('course_section_id', $resource->lesson->module->courseSection->id)
            ->exists();

        abort_unless($enrolled, 403);

        return Storage::disk('private')->download($resource->file_path, $resource->title);
    }
}
