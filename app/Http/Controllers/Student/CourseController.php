<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $sections = $request->user()
            ->enrollments()
            ->with(['courseSection.course', 'courseSection.instructor'])
            ->get()
            ->pluck('courseSection');

        return Inertia::render('Student/Courses/Index', [
            'sections' => $sections,
        ]);
    }

    public function show(Request $request, CourseSection $section): Response
    {
        abort_unless(
            $request->user()->enrollments()->where('course_section_id', $section->id)->exists(),
            403
        );

        $section->load([
            'course',
            'instructor',
            'modules' => fn ($q) => $q->whereNotNull('published_at')->orderBy('order_no'),
            'modules.lessons' => fn ($q) => $q->whereNotNull('published_at')->orderBy('order_no'),
            'assignments',
        ]);

        return Inertia::render('Student/Courses/Show', [
            'section' => $section,
        ]);
    }
}
