<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Notifications\TeacherRegisteredNotification;

class TeacherListController extends Controller
{
    /**
     * Display a listing of teachers for the organization.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $orgId = $user->organization_id;

        // Ensure only organization admins or super admins can access this page
        if (!$user->hasRole(['super-admin', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $query = User::query()
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'teacher');
            });

        // If not a global super-admin, scope to the user's organization
        if (!$user->hasRole('super-admin')) {
            $query->where('organization_id', $orgId);
        } else {
            // Super admins can see all teachers, load their organization relationship
            $query->with('organization:id,name');
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $sortField = $request->sort_field ?: 'created_at';
        $sortDirection = $request->sort_direction ?: 'desc';
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->per_page === 'all' ? $query->count() : ($request->per_page ?: 10);
        $teachers = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction'])
        ]);
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = Auth::user();

        // Ensure only organization admins or super admins can create teachers
        if (!$currentUser->hasRole(['super-admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $currentUser->organization_id, // Links to current admin's org
            'is_approved' => true,
        ]);

        $teacherRole = Role::where('slug', 'teacher')->first();
        if ($teacherRole) {
            $teacher->roles()->attach($teacherRole->id);
        }

        // Send email with credentials
        $teacher->notify(new TeacherRegisteredNotification($request->password));

        return back()->with('message', 'Teacher registered successfully.');
    }

    /**
     * Update the specified teacher's details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        // Ensure user is authorized to edit this teacher
        if (!$currentUser->hasRole(['super-admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!$currentUser->hasRole('super-admin') && $user->organization_id !== $currentUser->organization_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_approved' => 'required|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_approved' => $request->is_approved,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('message', 'Teacher account updated successfully.');
    }

    /**
     * Display the specified teacher's details.
     */
    public function show(User $user): Response
    {
        $currentUser = Auth::user();

        // Ensure user is authorized to view this teacher
        if (!$currentUser->hasRole('super-admin') && $user->organization_id !== $currentUser->organization_id) {
            abort(403);
        }

        $user->load(['roles', 'organization']);

        return Inertia::render('Admin/Teachers/Show', [
            'teacher' => $user
        ]);
    }
}
