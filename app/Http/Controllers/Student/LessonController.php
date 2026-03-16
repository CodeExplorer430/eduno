<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Content\Models\Lesson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function show(Request $request, Lesson $lesson): Response
    {
        abort_unless($lesson->published_at !== null, 404);

        $lesson->load(['module.courseSection', 'resources']);

        $enrolled = $request->user()
            ->enrollments()
            ->where('course_section_id', $lesson->module->courseSection->id)
            ->exists();

        abort_unless($enrolled, 403);

        $lesson->load(['module.courseSection.course']);

        return Inertia::render('Student/Lessons/Show', [
            'lesson' => $lesson,
        ]);
    }
}
