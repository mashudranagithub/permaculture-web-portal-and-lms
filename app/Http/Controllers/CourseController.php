<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
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
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') $perPage = Course::count() ?: 10;

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $courses = Course::query()
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
                'title' => $course->translate('title'),
                'slug' => $course->slug,
                'price' => $course->price,
                'level' => $course->level,
                'delivery_mode' => $course->delivery_mode,
                'is_online' => $course->is_online,
                'is_active' => $course->is_active,
                'status' => $course->status,
                'image_url' => $course->image ? asset('storage/' . $course->image) : null,
            ]);

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
}
