<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Content\Actions\DeleteResource;
use App\Domain\Content\Actions\UploadResource;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Content\Models\Resource;
use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\UploadResourceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function create(Request $request, CourseSection $section, Module $module, Lesson $lesson): Response
    {
        $this->authorize('create', Resource::class);

        return Inertia::render('Instructor/Resources/Upload', [
            'section' => $section,
            'module' => $module,
            'lesson' => $lesson,
        ]);
    }

    public function store(UploadResourceRequest $request, CourseSection $section, Module $module, Lesson $lesson, UploadResource $action): RedirectResponse
    {
        $this->authorize('create', Resource::class);

        $validated = $request->validated();

        /** @var UploadedFile $file */
        $file = $request->file('file');

        $action->execute($lesson->id, $validated['title'], $file, $validated['visibility']);

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Resource uploaded.');
    }

    public function destroy(Request $request, CourseSection $section, Module $module, Lesson $lesson, Resource $resource, DeleteResource $action): RedirectResponse
    {
        $this->authorize('delete', $resource);

        $action->execute($resource);

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Resource deleted.');
    }
}
