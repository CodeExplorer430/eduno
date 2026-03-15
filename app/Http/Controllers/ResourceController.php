<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Module\Actions\DeleteResource;
use App\Domain\Module\Actions\UploadResource;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use App\Http\Requests\Module\StoreResourceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function store(StoreResourceRequest $request, Lesson $lesson, UploadResource $action): RedirectResponse
    {
        $this->authorize('create', [Resource::class, $lesson]);

        $lesson->load('module');

        $action->handle(
            $lesson,
            $request->file('file'),
            $request->string('title')->toString(),
            ResourceVisibility::from($request->string('visibility')->toString()),
        );

        return redirect()->route('lessons.show', $lesson)
            ->with('success', 'Resource uploaded successfully.');
    }

    public function destroy(Resource $resource, DeleteResource $action): RedirectResponse
    {
        $this->authorize('delete', $resource);

        $lessonId = $resource->lesson_id;
        $action->handle($resource);

        return redirect()->route('lessons.show', $lessonId)
            ->with('success', 'Resource deleted.');
    }

    public function show(Resource $resource): RedirectResponse
    {
        $this->authorize('view', $resource);

        $url = Storage::disk('private')->temporaryUrl(
            $resource->file_path,
            now()->addMinutes(5)
        );

        return redirect()->away($url);
    }
}
