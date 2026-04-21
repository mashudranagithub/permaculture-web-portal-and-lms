<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BatchController extends Controller
{
    /**
     * Display a listing of the batches.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') $perPage = Batch::count() ?: 10;

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $batches = Batch::query()
            ->with(['course'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhereHas('course', function ($q) use ($search) {
                          $q->where('title->en', 'like', "%{$search}%")
                            ->orWhere('title->bn', 'like', "%{$search}%");
                      });
            })
            ->when($request->course_id, function ($query, $courseId) {
                $query->where('course_id', $courseId);
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (\App\Models\Batch $batch) => [
                'id' => $batch->id,
                'course_title' => $batch->course ? $batch->course->translate('title') : 'N/A',
                'title' => $batch->title,
                'start_date' => $batch->start_date ? $batch->start_date->format('d M, Y') : 'N/A',
                'end_date' => $batch->end_date ? $batch->end_date->format('d M, Y') : 'N/A',
                'capacity' => $batch->capacity,
                'enrolled_count' => $batch->enrollments()->count(),
                'price' => $batch->price,
                'status' => $batch->status,
                'is_enrollment_open' => $batch->is_enrollment_open,
            ]);

        $courses = Course::get(['id', 'title'])->map(fn(Course $c) => [
            'id' => $c->id,
            'title' => $c->translate('title')
        ]);

        return Inertia::render('Batches/Index', [
            'batches' => $batches,
            'courses' => $courses,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction', 'course_id'])
        ]);
    }

    /**
     * Show the form for creating a new batch.
     */
    public function create(): Response
    {
        $courses = Course::get(['id', 'title', 'price'])->map(fn(Course $c) => [
            'id' => $c->id,
            'title' => $c->translate('title'),
            'price' => $c->price
        ]);

        return Inertia::render('Batches/Create', [
            'courses' => $courses
        ]);
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_deadline' => 'nullable|date|before_or_equal:start_date',
            'capacity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'is_enrollment_open' => 'required|boolean',
        ]);

        Batch::create($validated);

        return redirect()->route('batches.index')
            ->with('message', 'Batch created successfully!');
    }

    /**
     * Show the form for editing the specified batch.
     */
    public function edit(Batch $batch): Response
    {
        $courses = Course::get(['id', 'title', 'price'])->map(fn(Course $c) => [
            'id' => $c->id,
            'title' => $c->translate('title'),
            'price' => $c->price
        ]);

        return Inertia::render('Batches/Edit', [
            'batch' => $batch,
            'courses' => $courses
        ]);
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_deadline' => 'nullable|date|before_or_equal:start_date',
            'capacity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'is_enrollment_open' => 'required|boolean',
        ]);

        $batch->update($validated);

        return redirect()->route('batches.index')
            ->with('message', 'Batch updated successfully!');
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        $batch->delete();
        return redirect()->route('batches.index')
            ->with('message', 'Batch deleted successfully!');
    }
}
