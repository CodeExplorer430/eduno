<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Content\Models\Lesson as ContentLesson;
use App\Domain\Content\Models\Module as ContentModule;
use App\Domain\Content\Models\Resource as ContentResource;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Models\Resource;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\CoursePolicy;
use App\Policies\CourseSectionPolicy;
use App\Policies\GradePolicy;
use App\Policies\LessonPolicy;
use App\Policies\ModulePolicy;
use App\Policies\ResourcePolicy;
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
        Gate::policy(CourseSection::class, CourseSectionPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(ContentModule::class, ModulePolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);
        Gate::policy(ContentLesson::class, LessonPolicy::class);
        Gate::policy(Resource::class, ResourcePolicy::class);
        Gate::policy(ContentResource::class, ResourcePolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Grade::class, GradePolicy::class);

        Gate::define('admin', fn (User $user) => $user->isAdmin());
    }
}
