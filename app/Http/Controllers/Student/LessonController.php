<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Module\Models\Lesson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function index(Request $request): Response
    {
        $sectionIds = $request->user()
            ->enrollments()
            ->where('status', 'active')
            ->pluck('course_section_id');

        $lessons = Lesson::whereHas('module', fn ($q) => $q->whereIn('course_section_id', $sectionIds))
            ->whereNotNull('published_at')
            ->with('module.section.course')
            ->orderBy('module_id')
            ->orderBy('order_no')
            ->get();

        return Inertia::render('Student/Lessons/Index', [
            'lessons' => $lessons,
        ]);
    }

    public function show(Request $request, Lesson $lesson): Response
    {
        abort_unless($lesson->published_at !== null, 404);

        $lesson->load(['module.section', 'resources']);

        $enrolled = $request->user()
            ->enrollments()
            ->where('course_section_id', $lesson->module->section->id)
            ->exists();

        abort_unless($enrolled, 403);

        $lesson->load(['module.section.course']);

        return Inertia::render('Student/Lessons/Show', [
            'lesson' => $lesson,
        ]);
    }
}
