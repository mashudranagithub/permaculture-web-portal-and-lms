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

Route::get('course-catalog', [CourseController::class, 'browse'])->name('courses.browse');

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Enrollment & Payments (Allow before verification)
    Route::post('enrollments', [\App\Http\Controllers\EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('payments/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('payments/{enrollment}/mock-success', [\App\Http\Controllers\PaymentController::class, 'mockSuccess'])->name('payments.mock-success');

    // bKash Routes
    Route::post('bkash/pay/{enrollment}', [\App\Http\Controllers\BkashController::class, 'pay'])->name('bkash.pay');
    Route::get('bkash/callback', [\App\Http\Controllers\BkashController::class, 'callback'])->name('bkash.callback');

    // SSLCommerz Routes
    Route::post('sslcommerz/pay/{enrollment}', [\App\Http\Controllers\SSLCommerzController::class, 'pay'])->name('sslcommerz.pay');
    Route::post('sslcommerz/success', [\App\Http\Controllers\SSLCommerzController::class, 'success'])->name('sslcommerz.success');
    Route::post('sslcommerz/fail', [\App\Http\Controllers\SSLCommerzController::class, 'fail'])->name('sslcommerz.fail');
    Route::post('sslcommerz/cancel', [\App\Http\Controllers\SSLCommerzController::class, 'cancel'])->name('sslcommerz.cancel');
    Route::post('sslcommerz/ipn', [\App\Http\Controllers\SSLCommerzController::class, 'ipn'])->name('sslcommerz.ipn');
});

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

        // Organization Settings (For Org Admins)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/payment', [\App\Http\Controllers\Organization\SettingsController::class, 'payment'])->name('payment');
            Route::post('/payment', [\App\Http\Controllers\Organization\SettingsController::class, 'updatePayment'])->name('payment.update');
        });
    });

    // Student Learning & Courses
    Route::get('my-courses', [\App\Http\Controllers\EnrollmentController::class, 'myCourses'])->name('enrollments.my-courses');
    Route::get('courses/{course}/learn', [\App\Http\Controllers\EnrollmentController::class, 'learn'])->name('enrollments.learn');
    Route::post('topics/{topic}/complete', [\App\Http\Controllers\EnrollmentController::class, 'completeTopic'])->name('topics.complete');

    // Media Uploads
    Route::post('/media/upload', [\App\Http\Controllers\MediaController::class, 'upload'])->name('media.upload');

    // Certificates
    Route::get('certificates', [\App\Http\Controllers\CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::get('certificates/{certificate}/download', [\App\Http\Controllers\CertificateController::class, 'download'])->name('certificates.download');

    // Students (Organization View)
    Route::get('students', [\App\Http\Controllers\Admin\StudentListController::class, 'index'])->name('admin.students.index');
    Route::post('students', [\App\Http\Controllers\Admin\StudentListController::class, 'store'])->name('admin.students.store');
    Route::get('students/{user}', [\App\Http\Controllers\Admin\StudentListController::class, 'show'])->name('admin.students.show');

    // Student Specific
    Route::get('my-certificates', [\App\Http\Controllers\CertificateController::class, 'studentCertificates'])->name('student.certificates');
});

// Public Certificate Verification
Route::get('verify/{token}', [\App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify');

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale');

require __DIR__.'/auth.php';
