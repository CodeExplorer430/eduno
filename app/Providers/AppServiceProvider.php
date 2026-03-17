<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\Course;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\CoursePolicy;
use App\Policies\GradePolicy;
use App\Policies\LessonPolicy;
use App\Policies\ModulePolicy;
use App\Policies\ResourcePolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

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

        Feature::define('high-contrast', function (User $user): bool {
            $prefs = $user->preferences()->first();

            return $prefs instanceof UserPreference && $prefs->high_contrast;
        });
        Feature::define('simplified-layout', function (User $user): bool {
            $prefs = $user->preferences()->first();

            return $prefs instanceof UserPreference && $prefs->simplified_layout;
        });

        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Grade::class, GradePolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);
        Gate::policy(Resource::class, ResourcePolicy::class);
    }
}
