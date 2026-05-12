<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        if (!$request->has('batch_id')) {
            return redirect()->route('courses.browse')->with('info', 'Please select a course to register.');
        }

        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', 'min:8'],
            'batch_id' => 'nullable|exists:batches,id',
        ]);

        $organizationId = null;
        if ($request->batch_id) {
            $batch = \App\Models\Batch::find($request->batch_id);
            $organizationId = $batch?->organization_id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $organizationId,
            'is_approved' => true, 
        ]);

        $studentRole = \App\Models\Role::where('slug', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        event(new Registered($user));

        Auth::login($user);

        // Auto-enroll if batch_id was provided
        if ($request->batch_id) {
            return redirect()->route('enrollments.store', ['batch_id' => $request->batch_id]);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
