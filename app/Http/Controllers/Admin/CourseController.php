<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Course\Actions\UpdateCourseStatus;
use App\Domain\Course\Models\Course;
use App\Http\Controllers\Controller;
use App\Support\Enums\CourseStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('viewAny', Course::class);

        $status = $request->query('status');

        $query = Course::with('sections')
            ->orderBy('code');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $courses = $query->paginate(25)->withQueryString();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => ['status' => $status],
            'statuses' => CourseStatus::cases(),
        ]);
    }

    public function updateStatus(Request $request, Course $course, UpdateCourseStatus $action): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('changeStatus', $course);

        $validated = $request->validate([
            'status' => ['required', new Enum(CourseStatus::class)],
        ]);

        $action->execute($course, CourseStatus::from($validated['status']), $request->user()->id);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course status updated.');
    }
}
