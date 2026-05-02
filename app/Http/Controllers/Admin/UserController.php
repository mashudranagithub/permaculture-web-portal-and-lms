<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users and their roles.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') $perPage = User::count() ?: 10;
        
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $users = User::with(['roles', 'permissions', 'organization:id,name'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'organization' => $user->organization ? [
                    'id' => $user->organization->id,
                    'name' => $user->organization->name,
                ] : null,
                'name' => $user->name,
                'email' => $user->email,
                'is_approved' => (bool)$user->is_approved,
                'roles' => $user->roles->pluck('name'),
                'role_ids' => $user->roles->pluck('id'),
                'permissions' => $user->permissions->pluck('name'),
                'permission_ids' => $user->permissions->pluck('id'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction']),
            'allRoles' => \App\Models\Role::all(['id', 'name']),
            'allPermissions' => \App\Models\Permission::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role_ids' => 'array',
            'permission_ids' => 'array',
        ]);

        $user->roles()->sync($request->role_ids);
        $user->permissions()->sync($request->permission_ids);

        return redirect()->back()->with('message', 'User account updated successfully!');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_ids' => 'array',
            'permission_ids' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'email_verified_at' => now(), // Manual creation assumes verified
            'is_approved' => true, // Manual creation assumes approved
        ]);

        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }

        if ($request->has('permission_ids')) {
            $user->permissions()->sync($request->permission_ids);
        }

        return redirect()->back()->with('message', 'User created successfully!');
    }

    /**
     * Approve a user.
     */
    public function approve(User $user): RedirectResponse
    {
        $user->update(['is_approved' => true]);

        return redirect()->back()->with('message', 'User account approved successfully!');
    }
}
