<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use App\Models\TopicProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Display the student's enrolled courses.
     */
    public function myCourses(): Response
    {
        $enrollments = Enrollment::where('user_id', Auth::id())
            ->with(['batch.course.topics' => fn($q) => $q->where('is_published', true)])
            ->latest()
            ->get()
            ->map(function($enr) {
                $topics = $enr->batch->course->topics;
                $totalTopics = $topics->count();
                
                $completedTopics = TopicProgress::where('user_id', Auth::id())
                    ->whereIn('topic_id', $topics->pluck('id'))
                    ->where('status', 'completed')
                    ->count();
                
                $progressPercent = $totalTopics > 0 ? min(100, round(($completedTopics / $totalTopics) * 100)) : 0;

                return [
                    'id' => $enr->id,
                    'enrollment_no' => $enr->enrollment_no,
                    'status' => $enr->status,
                    'payment_status' => $enr->payment_status,
                    'enrolled_at' => $enr->enrolled_at?->format('d M, Y'),
                    'progress_percent' => $progressPercent,
                    'completed_count' => $completedTopics,
                    'total_count' => $totalTopics,
                    'course' => [
                        'id' => $enr->batch->course->id,
                        'title' => $enr->batch->course->translate('title'),
                        'slug' => $enr->batch->course->slug,
                        'image_url' => $enr->batch->course->image_url,
                    ],
                    'batch' => [
                        'id' => $enr->batch->id,
                        'title' => $enr->batch->translate('title'),
                    ]
                ];
            });

        return Inertia::render('Student/MyCourses', [
            'enrollments' => $enrollments
        ]);
    }

    /**
     * Handle a new enrollment request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
        ]);

        $batch = Batch::findOrFail($request->batch_id);
        $user = Auth::user();

        // 1. Check if enrollment is open
        if (!$batch->is_enrollment_open) {
            return back()->with('error', 'Enrollment is currently closed for this batch.');
        }

        // 2. Check deadline
        if ($batch->enrollment_deadline && $batch->enrollment_deadline->isPast()) {
            return back()->with('error', 'Enrollment deadline has passed.');
        }

        // 3. Check already enrolled
        $existing = Enrollment::where('user_id', $user->id)
            ->where('batch_id', $batch->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already enrolled in this batch.');
        }

        // 4. Check seat capacity
        if ($batch->capacity > 0 && $batch->available_seats <= 0) {
            return back()->with('error', 'Sorry, this batch is already full.');
        }

        // 5. Calculate Final Price
        $price = $batch->price;
        if ($batch->discount_amount > 0) {
            $price = $batch->discount_type === 'percentage' 
                ? $price - ($price * ($batch->discount_amount / 100))
                : $price - $batch->discount_amount;
        }

        // 6. Create Enrollment
        $enrollment = Enrollment::create([
            'organization_id' => $batch->organization_id,
            'user_id' => $user->id,
            'batch_id' => $batch->id,
            'enrollment_no' => 'ENR-' . strtoupper(uniqid()),
            'price_at_enrollment' => $price,
            'status' => $price > 0 ? 'pending' : 'active',
            'payment_status' => $price > 0 ? 'pending' : 'paid',
            'enrolled_at' => $price > 0 ? null : now(),
        ]);

        if ($price > 0) {
            return redirect()->route('payments.initiate', ['enrollment_id' => $enrollment->id])
                ->with('message', 'Enrollment initiated. Please complete the payment.');
        }

        return redirect()->route('enrollments.my-courses')
            ->with('message', 'Successfully enrolled in ' . $batch->translate('title'));
    }

    /**
     * Show the learning interface for a course.
     */
    public function learn(Course $course, Request $request): Response
    {
        // 1. Verify Enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('batch_id', function($query) use ($course) {
                $query->select('id')->from('batches')->where('course_id', $course->id);
            })
            ->where('status', 'active')
            ->firstOrFail();

        // 2. Load Course with Topics and user's progress
        $course->load(['topics' => fn($q) => $q->where('is_published', true)->orderBy('order_index')]);
        
        $progress = TopicProgress::where('user_id', Auth::id())
            ->whereIn('topic_id', $course->topics->pluck('id'))
            ->get()
            ->keyBy('topic_id');

        return Inertia::render('Student/Learn', [
            'course' => [
                'id' => $course->id,
                'title' => $course->translate('title'),
                'topics' => $course->topics->map(fn($t) => array_merge($t->toArray(), [
                    'is_completed' => isset($progress[$t->id]) && $progress[$t->id]->status === 'completed'
                ]))
            ],
            'initialTopicId' => (int) $request->query('topic_id')
        ]);
    }

    /**
     * Mark a topic as completed for the authenticated user.
     */
    public function completeTopic(Topic $topic, Request $request): RedirectResponse
    {
        TopicProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'topic_id' => $topic->id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        return back()->with('message', 'Lesson marked as completed.');
    }
}
