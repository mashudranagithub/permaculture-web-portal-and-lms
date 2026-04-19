<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') $perPage = Role::count() ?: 10;

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $roles = Role::with('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'permissions' => $role->permissions->pluck('name'),
                'permission_ids' => $role->permissions->pluck('id'),
            ]);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction']),
            'allPermissions' => Permission::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permission_ids' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        return redirect()->back()->with('message', 'Role created successfully!');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permission_ids' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        $role->permissions()->sync($request->permission_ids);

        return redirect()->back()->with('message', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted.');
        }
        $role->delete();
        return redirect()->back()->with('message', 'Role deleted successfully!');
    }
}
