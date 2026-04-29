<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::with('student', 'faculty')
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, $role) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'manageableRoles' => User::manageableRoles(),
        ]);
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('users.create', [
            'manageableRoles' => User::manageableRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::manageableRoles())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::recordIfAdmin(
            'user_created',
            'user',
            $user->id,
            $user->email,
            "Created {$user->role} account for {$user->email}.",
            null,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        );

        return redirect()->route('users')->with('success', 'User account created successfully.');
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        $this->ensureUserIsDirectlyManageable($user);

        return view('users.edit', [
            'user' => $user,
            'manageableRoles' => User::manageableRoles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();
        $this->ensureUserIsDirectlyManageable($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(User::manageableRoles())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->isAdmin() && $validated['role'] !== User::ROLE_ADMIN && $this->isLastAdmin($user)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['role' => 'The last system admin account must remain an admin.']);
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        AuditLog::recordIfAdmin(
            'user_updated',
            'user',
            $user->id,
            $user->email,
            "Updated {$user->email} user account settings.",
            $oldValues,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password_changed' => ! empty($validated['password']),
            ]
        );

        return redirect()->route('users')->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureAdmin();
        $this->ensureUserIsDirectlyManageable($user);

        if ($user->is(auth()->user())) {
            return back()->withErrors(['delete' => 'You cannot delete the account you are currently signed in with.']);
        }

        if ($user->isAdmin() && $this->isLastAdmin($user)) {
            return back()->withErrors(['delete' => 'You cannot delete the last system admin account.']);
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->delete();

        AuditLog::recordIfAdmin(
            'user_deleted',
            'user',
            $user->id,
            $oldValues['email'],
            "Deleted {$oldValues['role']} account for {$oldValues['email']}.",
            $oldValues,
            null
        );

        return redirect()->route('users')->with('success', 'User account deleted successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), Response::HTTP_FORBIDDEN);
    }

    private function ensureUserIsDirectlyManageable(User $user): void
    {
        abort_if(
            $user->student()->exists() || $user->faculty()->exists(),
            Response::HTTP_FORBIDDEN,
            'Student and faculty-linked accounts should be managed from their dedicated modules.'
        );
    }

    private function isLastAdmin(User $user): bool
    {
        return $user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() === 1;
    }
}
