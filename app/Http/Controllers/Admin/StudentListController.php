<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class StudentListController extends Controller
{
    /**
     * Display a listing of students for the organization.
     */
    public function index(Request $request): Response
    {
        $orgId = Auth::user()->organization_id;

        $query = User::where('organization_id', $orgId)
            ->where(function($q) {
                $q->whereHas('roles', fn($qr) => $qr->where('slug', 'student'))
                  ->orWhereHas('enrollments');
            })
            ->with(['enrollments.batch.course'])
            ->withCount('enrollments');

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
        $users = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Students/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction'])
        ]);
    }

    /**
     * Display the specified student details.
     */
    public function show(User $user): Response
    {
        // Ensure user belongs to the same organization
        if ($user->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        $user->load([
            'roles',
            'enrollments.batch.course',
            'enrollments.payments' => fn($q) => $q->orderBy('created_at', 'desc'),
            'payments' => fn($q) => $q->orderBy('created_at', 'desc')
        ]);

        // Get certificates for this user
        $certificates = Certificate::where('user_id', $user->id)
            ->with(['course', 'batch'])
            ->get();

        return Inertia::render('Admin/Students/Show', [
            'student' => $user,
            'certificates' => $certificates
        ]);
    }
}
