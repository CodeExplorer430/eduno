<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Report\Actions\GetAdminReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request, GetAdminReport $action): Response
    {
        $this->authorize('admin');

        return Inertia::render('Admin/Reports/Index', [
            'report' => $action->handle(),
        ]);
    }
}
