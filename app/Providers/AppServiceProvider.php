<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\CoursePolicy;
use App\Policies\GradePolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Grade::class, GradePolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
    }
}
