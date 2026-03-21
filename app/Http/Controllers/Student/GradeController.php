<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function index(Request $request): Response
    {
        $grades = $request->user()
            ->submissions()
            ->with(['assignment.courseSection.course', 'grade'])
            ->whereHas('grade', fn ($q) => $q->whereNotNull('released_at'))
            ->get()
            ->pluck('grade')
            ->filter();

        return Inertia::render('Student/Grades/Index', [
            'grades' => $grades->values(),
        ]);
    }
}
