<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

use App\Http\Controllers\CourseController;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('courses', CourseController::class);
    Route::get('courses/{course}/curriculum', [CourseController::class, 'curriculum'])->name('courses.curriculum');
    
    Route::resource('batches', \App\Http\Controllers\BatchController::class);

    // Topic Management
    Route::post('topics/reorder', [\App\Http\Controllers\TopicController::class, 'reorder'])->name('topics.reorder');
    Route::resource('topics', \App\Http\Controllers\TopicController::class)->except(['index', 'create', 'show', 'edit']);

    Route::middleware(['role:super-admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::middleware('permission:manage-users')->group(function () {
            Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
            Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
            Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
        });

        // Organization Management (LMS Admin)
        Route::prefix('organizations')->name('organizations.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Organization\OrganizationController::class, 'index'])->name('index');
            Route::get('/approvals',           [\App\Http\Controllers\Organization\OrganizationController::class, 'approvalQueue'])->name('queue');
            Route::get('/{organization}',      [\App\Http\Controllers\Organization\OrganizationController::class, 'show'])->name('show');
            Route::post('/{organization}/approve',   [\App\Http\Controllers\Organization\OrganizationController::class, 'approve'])->name('approve');
            Route::post('/{organization}/reject',    [\App\Http\Controllers\Organization\OrganizationController::class, 'reject'])->name('reject');
            Route::post('/{organization}/suspend',   [\App\Http\Controllers\Organization\OrganizationController::class, 'suspend'])->name('suspend');
            Route::post('/{organization}/reactivate',[\App\Http\Controllers\Organization\OrganizationController::class, 'reactivate'])->name('reactivate');
            Route::delete('/{organization}',   [\App\Http\Controllers\Organization\OrganizationController::class, 'destroy'])->name('destroy');
        });

        // Roles & Permissions
        Route::middleware('permission:manage-roles')->resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::middleware('permission:manage-permissions')->resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);

        // PDF Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/users/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadUsers'])->name('users.pdf')->middleware('permission:manage-users');
            Route::get('/roles/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadRoles'])->name('roles.pdf')->middleware('permission:manage-roles');
            Route::get('/permissions/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadPermissions'])->name('permissions.pdf')->middleware('permission:manage-permissions');
            Route::get('/courses/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadCourses'])->name('courses.pdf');
            Route::get('/batches/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadBatches'])->name('batches.pdf');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Media Uploads
    Route::post('/media/upload', [\App\Http\Controllers\MediaController::class, 'upload'])->name('media.upload');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale');

require __DIR__.'/auth.php';
