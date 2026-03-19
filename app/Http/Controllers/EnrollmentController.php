<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Course\Actions\EnrollStudent;
use App\Domain\Course\Actions\UnenrollStudent;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class EnrollmentController extends Controller
{
    public function store(CourseSection $section, EnrollStudent $action): RedirectResponse
    {
        $this->authorize('enroll', $section);

        /** @var User $user */
        $user = auth()->user();

        $action->handle($user, $section);

        return redirect()->back()
            ->with('success', 'Enrolled successfully.');
    }

    public function destroy(CourseSection $section, UnenrollStudent $action): RedirectResponse
    {
        $this->authorize('unenroll', $section);

        /** @var User $user */
        $user = auth()->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_section_id', $section->id)
            ->firstOrFail();

        $action->handle($enrollment);

        return redirect()->back()
            ->with('success', 'Unenrolled successfully.');
    }
}
