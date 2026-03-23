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
            ->with(['section.course', 'section.instructor'])
            ->get()
            ->pluck('section');

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

        $userId = $request->user()->id;

        $section->load([
            'course',
            'instructor:id,name,email',
            'modules' => fn ($q) => $q->whereNotNull('published_at')->orderBy('order_no'),
            'modules.lessons' => fn ($q) => $q->whereNotNull('published_at')->orderBy('order_no'),
            'announcements' => fn ($q) => $q->whereNotNull('published_at')->orderByDesc('published_at')->limit(10),
            'assignments' => fn ($q) => $q->whereNotNull('published_at')->with([
                'submissions' => fn ($sq) => $sq->where('student_id', $userId),
            ]),
            'enrollments' => fn ($q) => $q->where('status', 'active'),
        ]);

        return Inertia::render('Student/Courses/Show', [
            'section'       => $section,
            'announcements' => $section->announcements->values(),
            'assignments'   => $section->assignments->values(),
        ]);
    }
}
