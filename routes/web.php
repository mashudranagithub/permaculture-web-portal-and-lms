<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\PortalSetting;

use App\Models\Course;
use App\Models\Organization;

Route::get('/', function () {
    $courses = Course::active()->with('organization')->take(3)->get()->map(function (Course $course) {
        return [
            'id' => $course->id,
            'title' => $course->translate('title'),
            'slug' => $course->slug,
            'short_description' => $course->translate('short_description'),
            'image_url' => $course->image_url,
            'price' => $course->price,
            'level' => $course->level,
            'organization_name' => $course->organization ? $course->organization->name : null,
        ];
    });

    $partners = Organization::where('status', 'active')->take(6)->get()->map(function ($partner) {
        return [
            'id' => $partner->id,
            'name' => $partner->name,
            'slug' => $partner->slug,
            'logo_url' => $partner->logo_url,
        ];
    });

    $ethics = PortalSetting::getValue('about_ethics', []);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'hero_title' => PortalSetting::getValue('homepage_hero_title', 'Cultivating a Sustainable Future'),
        'hero_description' => PortalSetting::getValue('homepage_hero_description', 'Join thousands of changemakers worldwide in designing nature-based systems that regenerate our planet and communities.'),
        'about_lms' => PortalSetting::getValue('about_lms', 'Our LMS provides structured learning pathways, peer discussions, and verified certificates to empower local regenerative initiatives.'),
        'counter_courses' => PortalSetting::getValue('counter_courses', '10+'),
        'counter_teachers' => PortalSetting::getValue('counter_teachers', '25+'),
        'counter_students' => PortalSetting::getValue('counter_students', '500+'),
        'counter_batches' => PortalSetting::getValue('counter_batches', '50+'),
        'courses' => $courses,
        'partners' => $partners,
        'ethics' => $ethics,
        'homepage_slides' => PortalSetting::getValue('homepage_slides', []),
    ]);
});

use App\Http\Controllers\CourseController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\PortalSettingsController;

Route::get('course-catalog', [CourseController::class, 'browse'])->name('courses.browse');
Route::get('about', [PublicPageController::class, 'about'])->name('about');
Route::get('partners', [PublicPageController::class, 'partners'])->name('partners.index');
Route::get('partners/{slug}', [PublicPageController::class, 'partnerDetails'])->name('partners.show');
Route::get('terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('privacy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('contact', [PublicPageController::class, 'contact'])->name('contact');

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
        Route::middleware('role:super-admin')->prefix('organizations')->name('organizations.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Organization\OrganizationController::class, 'index'])->name('index');
            Route::get('/approvals',           [\App\Http\Controllers\Organization\OrganizationController::class, 'approvalQueue'])->name('queue');
            Route::get('/{organization}',      [\App\Http\Controllers\Organization\OrganizationController::class, 'show'])->name('show');
            Route::get('/{organization}/edit', [\App\Http\Controllers\Organization\OrganizationController::class, 'edit'])->name('edit');
            Route::post('/{organization}/update', [\App\Http\Controllers\Organization\OrganizationController::class, 'update'])->name('update');
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
            Route::get('/organization', [\App\Http\Controllers\Organization\SettingsController::class, 'editOrganization'])->name('organization');
            Route::post('/organization', [\App\Http\Controllers\Organization\SettingsController::class, 'updateOrganization'])->name('organization.update');
        });

        // Portal CMS (For Super & LMS Admins only)
        Route::prefix('cms')->name('cms.')->group(function () {
            Route::get('/homepage', [PortalSettingsController::class, 'editHomepage'])->name('homepage.edit');
            Route::post('/homepage', [PortalSettingsController::class, 'updateHomepage'])->name('homepage.update');

            Route::get('/about', [PortalSettingsController::class, 'editAbout'])->name('about.edit');
            Route::post('/about', [PortalSettingsController::class, 'updateAbout'])->name('about.update');

            Route::get('/terms', [PortalSettingsController::class, 'editTerms'])->name('terms.edit');
            Route::post('/terms', [PortalSettingsController::class, 'updateTerms'])->name('terms.update');

            Route::get('/privacy', [PortalSettingsController::class, 'editPrivacy'])->name('privacy.edit');
            Route::post('/privacy', [PortalSettingsController::class, 'updatePrivacy'])->name('privacy.update');

            Route::get('/courses-header', [PortalSettingsController::class, 'editCoursesHeader'])->name('courses-header.edit');
            Route::post('/courses-header', [PortalSettingsController::class, 'updateCoursesHeader'])->name('courses-header.update');

            Route::get('/partners-header', [PortalSettingsController::class, 'editPartnersHeader'])->name('partners-header.edit');
            Route::post('/partners-header', [PortalSettingsController::class, 'updatePartnersHeader'])->name('partners-header.update');

            Route::get('/contact', [PortalSettingsController::class, 'editContact'])->name('contact.edit');
            Route::post('/contact', [PortalSettingsController::class, 'updateContact'])->name('contact.update');
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

    // Teachers (Organization View)
    Route::get('teachers', [\App\Http\Controllers\Admin\TeacherListController::class, 'index'])->name('admin.teachers.index');
    Route::post('teachers', [\App\Http\Controllers\Admin\TeacherListController::class, 'store'])->name('admin.teachers.store');
    Route::get('teachers/{user}', [\App\Http\Controllers\Admin\TeacherListController::class, 'show'])->name('admin.teachers.show');
    Route::patch('teachers/{user}', [\App\Http\Controllers\Admin\TeacherListController::class, 'update'])->name('admin.teachers.update');

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
