<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PortalSetting;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') $perPage = Course::count() ?: 10;

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $courses = Course::query()
            ->with('organization:id,name')
            // Scope to organization for students and organization admins
            ->when(!$user->hasRole('super-admin'), function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('title->en', 'like', "%{$search}%")
                      ->orWhere('title->bn', 'like', "%{$search}%")
                      ->orWhere('description->en', 'like', "%{$search}%")
                      ->orWhere('description->bn', 'like', "%{$search}%");
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($course) => [
                'id' => $course->id,
                'organization' => $course->organization ? [
                    'id' => $course->organization->id,
                    'name' => $course->organization->name,
                ] : null,
                'title' => $course->translate('title'),
                'slug' => $course->slug,
                'price' => $course->price,
                'level' => $course->level,
                'delivery_mode' => $course->delivery_mode,
                'is_online' => $course->is_online,
                'is_active' => $course->is_active,
                'status' => $course->status,
                'image_url' => $course->image_url,
            ]);

        if ($user->hasRole('student')) {
            $enrollments = Enrollment::where('user_id', $user->id)
                ->join('batches', 'enrollments.batch_id', '=', 'batches.id')
                ->select('batches.course_id', 'enrollments.status', 'enrollments.id as enrollment_id')
                ->get()
                ->keyBy('course_id');

            $studentCourses = Course::active()
                ->where('organization_id', $user->organization_id)
                ->with(['activeBatches'])
                ->get()
                ->map(function($c) use ($enrollments) {
                    $enrollment = $enrollments->get($c->id);
                    return [
                        'id' => $c->id,
                        'title' => $c->translate('title'),
                        'short_description' => $c->translate('short_description'),
                        'level' => $c->level,
                        'image_url' => $c->image_url,
                        'is_enrolled' => !!$enrollment,
                        'enrollment_status' => $enrollment ? $enrollment->status : null,
                        'enrollment_id' => $enrollment ? $enrollment->enrollment_id : null,
                        'active_batches' => $c->activeBatches->map(fn($b) => [
                            'id' => $b->id,
                            'price' => $b->price,
                            'title' => $b->translate('title')
                        ])
                    ];
                });

            return Inertia::render('Courses/StudentBrowse', [
                'courses' => $studentCourses
            ]);
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction'])
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Response
    {
        return Inertia::render('Courses/Create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title.en' => 'required|string|max:255',
            'title.bn' => 'required|string|max:255',
            'description.en' => 'required|string',
            'description.bn' => 'required|string',
            'short_description.en' => 'required|string|max:500',
            'short_description.bn' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:Foundation,Intermediate,Advanced',
            'delivery_mode' => 'required|in:online,offline,hybrid',
            'duration' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']['en']);
        $validated['is_online'] = ($validated['delivery_mode'] === 'online' || $validated['delivery_mode'] === 'hybrid');
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses/thumbs', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('message', 'Course created successfully!');
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): Response
    {
        return Inertia::render('Courses/Edit', [
            'course' => $course
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title.en' => 'required|string|max:255',
            'title.bn' => 'required|string|max:255',
            'description.en' => 'required|string',
            'description.bn' => 'required|string',
            'short_description.en' => 'required|string|max:500',
            'short_description.bn' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:Foundation,Intermediate,Advanced',
            'delivery_mode' => 'required|in:online,offline,hybrid',
            'duration' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']['en']);
        $validated['is_online'] = ($validated['delivery_mode'] === 'online' || $validated['delivery_mode'] === 'hybrid');

        if ($request->hasFile('image')) {
            if ($course->image) \Illuminate\Support\Facades\Storage::disk('public')->delete($course->image);
            $validated['image'] = $request->file('image')->store('courses/thumbs', 'public');
        }
        if ($request->hasFile('banner_image')) {
            if ($course->banner_image) \Illuminate\Support\Facades\Storage::disk('public')->delete($course->banner_image);
            $validated['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('message', 'Course updated successfully!');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();
        return redirect()->route('courses.index')
            ->with('message', 'Course deleted successfully!');
    }

    /**
     * Show the curriculum management page for a course.
     */
    public function curriculum(Course $course): Response
    {
        $course->load(['topics' => fn($q) => $q->orderBy('order_index')]);
        
        return Inertia::render('Courses/Curriculum', [
            'course' => [
                'id' => $course->id,
                'title' => $course->translate('title'),
                'topics' => $course->topics
            ]
        ]);
    }

    /**
     * Display a public-style course catalog for logged-in students.
     */
    public function browse(): Response
    {
        $user = auth()->user();
        $courses = Course::active()
            ->with(['organization', 'activeBatches'])
            ->get()
            ->map(function (Course $course) use ($user) {
                $isEnrolled = false;
                $pendingEnrollmentId = null;

                if ($user) {
                    $isEnrolled = \App\Models\Enrollment::where('user_id', $user->id)
                        ->whereIn('batch_id', $course->batches->pluck('id'))
                        ->where('status', 'active')
                        ->exists();
                    
                    $pendingEnrollmentId = \App\Models\Enrollment::where('user_id', $user->id)
                        ->whereIn('batch_id', $course->batches->pluck('id'))
                        ->where('status', 'pending')
                        ->value('id');
                }

                return [
                    'id' => $course->id,
                    'title' => $course->translate('title'),
                    'short_description' => $course->translate('short_description'),
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'delivery_mode' => $course->delivery_mode,
                    'image_url' => $course->image_url,
                    'organization' => $course->organization ? [
                        'name' => $course->organization->name,
                        'initials' => collect(explode(' ', $course->organization->name))->map(fn($n) => mb_substr($n, 0, 1))->join(''),
                    ] : null,
                    'is_enrolled' => $isEnrolled,
                    'pending_enrollment_id' => $pendingEnrollmentId,
                    'active_batches' => $course->activeBatches->map(fn($batch) => [
                        'id' => $batch->id,
                        'title' => $batch->translate('title'),
                        'price' => $batch->price,
                        'start_date' => $batch->start_date?->format('d M, Y'),
                        'available_seats' => $batch->available_seats,
                    ])
                ];
            });

        $coursesHeader = PortalSetting::getValue('courses_header', [
            'title' => ['en' => 'Available Courses', 'bn' => 'প্রাপ্য কোর্সসমূহ'],
            'subtitle' => ['en' => 'Discover the perfect path for your regenerative journey.', 'bn' => 'আপনার পুনরুত্পাদনশীল যাত্রার জন্য নিখুঁত পথটি আবিষ্কার করুন।'],
            'badge' => ['en' => 'Interactive Learning', 'bn' => 'ইন্টারেক্টিভ লার্নিং'],
            'bg_image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1920&q=80'
        ]);
        $locale = app()->getLocale();
        $header = [
            'title' => $coursesHeader['title'][$locale] ?? $coursesHeader['title']['en'] ?? '',
            'subtitle' => $coursesHeader['subtitle'][$locale] ?? $coursesHeader['subtitle']['en'] ?? '',
            'badge' => $coursesHeader['badge'][$locale] ?? $coursesHeader['badge']['en'] ?? '',
            'bg_image' => $coursesHeader['bg_image'] ?? 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1920&q=80',
        ];
        if ($header['bg_image'] && !str_starts_with($header['bg_image'], 'http')) {
            $header['bg_image'] = asset('storage/' . $header['bg_image']);
        }

        return Inertia::render('Courses/Browse', [
            'header' => $header,
            'courses' => $courses
        ]);
    }
}
