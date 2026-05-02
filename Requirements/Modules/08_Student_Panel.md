# Module: Student Panel
**Phase:** 2  
**Depends On:** Organization Module (M1), Enrollment & Batch (existing)  
**Priority:** 🔴 High  

---

## 1. Overview

The Student Panel is the learner-facing interface. A student is a user with the `student` role who belongs to a specific organization and is enrolled in one or more courses/batches.

**Key Principles:**
- Students only see content from their own organization
- Students only access topic content for courses they are enrolled in
- Batch visibility is limited to the batch they are assigned to
- Progress is tracked per topic

---

## 2. Student User Attributes

### Additional fields on `users` table (existing + new):

| Column | Type | Notes |
|---|---|---|
| `organization_id` | FK | Already planned in M1 |
| `student_id` | string nullable | Org-assigned student ID |
| `phone` | string nullable | Contact number |
| `date_of_birth` | date nullable | |
| `gender` | enum nullable | `male`, `female`, `other` |
| `profile_bio` | text nullable | Short bio |
| `is_active` | boolean | Whether student account is active |

---

## 3. Enrollment Model (Enhancement)

The existing `Enrollment` model needs richer relationships:

```php
class Enrollment extends Model
{
    // Existing
    user_id, batch_id, payment_id, status, progress_percentage,
    completed_at, last_accessed_at

    // New
    enrolled_at,        // timestamp — when enrollment was confirmed
    access_expires_at,  // timestamp nullable — for time-limited access
}
```

---

## 4. Topic Progress Tracking

The existing `TopicProgress` model tracks completion per topic per user.

**Ensure schema has:**

| Column | Type | Notes |
|---|---|---|
| `id` | PK | |
| `user_id` | FK → users | |
| `topic_id` | FK → topics | |
| `batch_id` | FK → batches | |
| `is_completed` | boolean | |
| `completed_at` | timestamp nullable | |
| `time_spent_minutes` | int default 0 | Optional: watch time |
| `timestamps` | | |

---

## 5. Routes

```php
// Student-only routes (auth + student role middleware)
Route::middleware(['auth', 'role:student', 'org.approved'])->prefix('student')->group(function () {
    Route::get('/dashboard',          [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/courses',            [StudentCourseController::class, 'index'])->name('student.courses');
    Route::get('/courses/{course}',   [StudentCourseController::class, 'show'])->name('student.courses.show');
    Route::get('/topics/{topic}',     [StudentTopicController::class, 'show'])->name('student.topics.show');
    Route::post('/topics/{topic}/complete', [StudentTopicController::class, 'markComplete'])->name('student.topics.complete');
    Route::get('/batch',              [StudentBatchController::class, 'show'])->name('student.batch');
    Route::get('/batchmates',         [StudentBatchController::class, 'batchmates'])->name('student.batchmates');
    Route::get('/profile',            [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::put('/profile',            [StudentProfileController::class, 'update'])->name('student.profile.update');
});
```

---

## 6. Controllers

### `StudentDashboardController`
**`index()`** — Returns:
- Enrolled courses count
- Completed topics count
- Overall progress %
- Recent activity (last accessed topics)
- Upcoming batch sessions

### `StudentCourseController`
**`index()`** — Returns:
  - All courses the student is enrolled in (via enrollments → batches → courses)
  - Scoped to `organization_id`

**`show($course)`** — Returns:
  - Course details
  - Topic list (only for enrolled students)
  - Progress per topic
  - Access gate: if not enrolled → redirect with message

### `StudentTopicController`
**`show($topic)`** — Returns:
  - Topic content (text, audio, PDF, video)
  - Access gate: must be enrolled in a batch that belongs to this course
  - Updates `last_accessed_at` on enrollment

**`markComplete($topic)`** — Marks topic as complete and updates progress %.

### `StudentBatchController`
**`show()`** — Returns:
  - Student's current batch details
  - Schedule (class routine)
  - Session links (Zoom/Meet)

**`batchmates()`** — Returns:
  - List of co-students in the same batch
  - Name + avatar only (privacy-focused)

### `StudentProfileController`
- Edit name, phone, avatar, bio
- Cannot change email (locked)

---

## 7. Authorization & Access Gates

### Topic Access Check
A student can only view a topic if:
1. The topic belongs to a course
2. The course belongs to a batch
3. The student is enrolled in that batch with `status = active`

```php
// TopicPolicy.php
public function view(User $user, Topic $topic): bool
{
    return $user->enrollments()
        ->whereHas('batch', fn($q) => $q->where('course_id', $topic->course_id))
        ->where('status', 'active')
        ->exists();
}
```

### Organization Scoping
All data shown to students must also pass the `OrganizationScope` to ensure cross-org isolation.

---

## 8. UI Pages (`Pages/Student/`)

### Dashboard (`Dashboard.vue`)
**Widgets:**
- Progress ring (overall % complete)
- "Continue Learning" card → last accessed topic
- My Enrolled Courses (grid cards)
- Upcoming Batch Sessions
- Notifications (future)

### Courses List (`Courses/Index.vue`)
- Cards showing enrolled courses
- Progress bar per course
- "Continue" button → resumes last accessed topic

### Course Detail (`Courses/Show.vue`)
- Course info header (title, description, instructor)
- Topic list (accordion style)
  - ✅ Completed topics (green)
  - 🔒 Locked topics (if sequential learning enforced)
  - ▶ Next topic CTA
- Sidebar: batch info, schedule

### Topic View (`Topics/Show.vue`)
- Renders content based on `topic_type`:
  - **content** → Rich HTML renderer
  - **pdf** → Embedded PDF viewer (iframe or pdf.js)
  - **audio** → Native `<audio>` player
  - **video** → YouTube embed
  - **online_class** → Meeting link + join button
  - **quiz** → Quiz UI (see Quiz spec below)
  - **assignment** → Assignment instructions + file upload
- "Mark as Complete" button
- Previous / Next topic navigation

### Batch View (`Batch/Show.vue`)
- Batch title, dates, status
- Weekly schedule table
- "Join Class" buttons for live sessions
- Batchmates section (avatar grid)

### Profile (`Profile/Edit.vue`)
- Name, phone, avatar, bio fields
- Password change section

---

## 9. Quiz Student Experience

When a student opens a `quiz` topic:
1. Quiz questions are loaded from `topics.quiz_data`
2. Student selects answers (MCQ or True/False)
3. On submit:
   - Answers are evaluated client-side (or server-side for security)
   - Score is shown with explanations
   - Topic is marked complete if passing score achieved (configurable)
4. Results are saved to a `quiz_attempts` table (future enhancement)

---

## 10. Acceptance Criteria

- [ ] Student login redirects to `/student/dashboard`
- [ ] Student sees only courses from their own organization
- [ ] Course topics are only visible if student is enrolled in a batch for that course
- [ ] Students cannot access other organizations' content even by URL manipulation
- [ ] Topic progress is tracked and persisted
- [ ] Course progress % updates as topics are completed
- [ ] Batch view shows only the student's assigned batch
- [ ] Batchmates list shows only students in the same batch
- [ ] Students can update their profile (not email)
- [ ] LMS Admin and Org Admin see student panel is separate and correctly isolated
