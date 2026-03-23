<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Audit\Actions\LogAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('viewAny', User::class);

        $search = $request->query('search');
        $role   = $request->query('role');

        $query = User::orderBy('name');

        if ($search !== null && $search !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $query->where(function ($q) use ($escaped): void {
                $q->where('name', 'ilike', "%{$escaped}%")
                    ->orWhere('email', 'ilike', "%{$escaped}%");
            });
        }

        if ($role !== null && $role !== '') {
            $query->where('role', $role);
        }

        $users = $query->paginate(25)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users'   => $users,
            'filters' => ['search' => $search, 'role' => $role],
            'summary' => [
                'total'      => User::count(),
                'student'    => User::where('role', 'student')->count(),
                'instructor' => User::where('role', 'instructor')->count(),
                'admin'      => User::where('role', 'admin')->count(),
            ],
            'roles' => array_map(
                fn ($r) => ['name' => ucfirst($r->value), 'value' => $r->value],
                UserRole::cases()
            ),
        ]);
    }

    public function edit(Request $request, User $user): Response
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('update', $user);

        return Inertia::render('Admin/Users/Edit', [
            'user'  => $user,
            'roles' => array_map(
                fn ($r) => ['name' => ucfirst($r->value), 'value' => $r->value],
                UserRole::cases()
            ),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
        ]);

        $oldRole = $user->role->value;
        $user->update(['role' => $validated['role']]);

        $this->logAction->execute(
            $request->user()->id,
            'user.role_changed',
            User::class,
            $user->id,
            ['from' => $oldRole, 'to' => $validated['role']],
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User role updated.');
    }
}
