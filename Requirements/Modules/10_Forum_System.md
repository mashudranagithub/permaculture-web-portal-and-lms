# Module: Forum / Discussion System
**Phase:** 4 (Planned — Not Immediate)  
**Depends On:** Student Panel (M3), Organization Module (M1)  
**Priority:** 🟢 Low (Future)  

---

## 1. Overview

A discussion forum that allows students to communicate with each other and instructors within the context of a course or a specific topic. Forums are scoped to an organization.

---

## 2. Forum Scope & Access Rules

| Actor | Can Do |
|---|---|
| Student | Post threads & replies in courses they're enrolled in |
| Org Admin | Moderate (pin, delete, lock) all threads in their org |
| LMS Admin | View all forums across all orgs; override moderation |
| Unenrolled user | Cannot access forum — redirected to enrollment page |

---

## 3. Database Schema

### Table: `forum_threads`

| Column | Type | Notes |
|---|---|---|
| `id` | PK | |
| `course_id` | FK → courses | Which course does this thread belong to |
| `topic_id` | FK → topics nullable | Optional: thread tied to a specific lesson |
| `organization_id` | FK → organizations | Tenant scoping |
| `user_id` | FK → users | Thread author |
| `title` | string | Thread title |
| `body` | longtext | Thread content (markdown or HTML) |
| `is_pinned` | boolean default false | Org admin can pin |
| `is_locked` | boolean default false | No new replies if locked |
| `is_resolved` | boolean default false | Marks thread as answered |
| `views_count` | int default 0 | |
| `replies_count` | int default 0 | Cached counter |
| `last_reply_at` | timestamp nullable | |
| `timestamps` | | |
| `soft_deletes` | | |

### Table: `forum_replies`

| Column | Type | Notes |
|---|---|---|
| `id` | PK | |
| `thread_id` | FK → forum_threads | |
| `user_id` | FK → users | Reply author |
| `body` | longtext | Reply content |
| `is_solution` | boolean default false | Marked as best answer by thread author |
| `parent_id` | FK → forum_replies nullable | For nested replies |
| `timestamps` | | |
| `soft_deletes` | | |

### Table: `forum_reactions` (Optional Enhancement)

| Column | Type | Notes |
|---|---|---|
| `id` | PK | |
| `user_id` | FK | |
| `reactable_id` | int | Polymorphic |
| `reactable_type` | string | `ForumThread` or `ForumReply` |
| `type` | enum | `like`, `helpful` |

---

## 4. Routes

```php
Route::middleware(['auth', 'org.approved'])->prefix('forum')->group(function () {
    // Thread routes
    Route::get('/{course}',                [ForumThreadController::class, 'index'])->name('forum.index');
    Route::get('/{course}/create',         [ForumThreadController::class, 'create'])->name('forum.create');
    Route::post('/{course}',               [ForumThreadController::class, 'store'])->name('forum.store');
    Route::get('/thread/{thread}',         [ForumThreadController::class, 'show'])->name('forum.thread.show');
    Route::delete('/thread/{thread}',      [ForumThreadController::class, 'destroy'])->name('forum.thread.destroy');

    // Reply routes
    Route::post('/thread/{thread}/reply',  [ForumReplyController::class, 'store'])->name('forum.reply.store');
    Route::delete('/reply/{reply}',        [ForumReplyController::class, 'destroy'])->name('forum.reply.destroy');
    Route::post('/reply/{reply}/solution', [ForumReplyController::class, 'markSolution'])->name('forum.reply.solution');

    // Moderation (org-admin only)
    Route::post('/thread/{thread}/pin',    [ForumModerationController::class, 'pin'])->name('forum.thread.pin');
    Route::post('/thread/{thread}/lock',   [ForumModerationController::class, 'lock'])->name('forum.thread.lock');
});
```

---

## 5. Models

### `ForumThread`
```php
class ForumThread extends Model
{
    use SoftDeletes;
    
    protected static function booted(): void
    {
        // Auto-apply organization scope
        static::addGlobalScope(new OrganizationScope);
    }

    public function course(): BelongsTo { ... }
    public function topic(): BelongsTo { ... }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function replies(): HasMany { ... }
    public function latestReply(): HasOne { ... }
}
```

### `ForumReply`
```php
class ForumReply extends Model
{
    use SoftDeletes;

    public function thread(): BelongsTo { ... }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function parent(): BelongsTo { return $this->belongsTo(ForumReply::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(ForumReply::class, 'parent_id'); }
}
```

---

## 6. UI Pages (`Pages/Forum/`)

### `Index.vue` — Course Forum
- Thread list (title, author, reply count, last reply)
- Filter by topic (linked to curriculum topics)
- Pin indicator
- "New Thread" button (enrolled students only)
- Search threads

### `Show.vue` — Thread View
- Thread body with author info
- Reply list (nested up to 2 levels)
- Reply form at bottom (if thread not locked)
- "Mark as Solution" button (for thread author)
- Admin moderation toolbar (pin, lock, delete)

### `Create.vue` — New Thread Form
- Title input
- Body (RichText or Markdown)
- Topic selector (optional link to a specific lesson)

---

## 7. Notifications

| Event | Notification |
|---|---|
| New reply on student's thread | Email + in-app |
| Reply marked as solution | Email to reply author |
| New thread in org (for org-admin) | In-app badge |

---

## 8. Acceptance Criteria

- [ ] Students can only post in forums for courses they are enrolled in
- [ ] Threads are scoped to organization — cross-org isolation enforced
- [ ] Org admin can pin, lock, and delete any thread in their org
- [ ] Thread author can mark a reply as the solution
- [ ] Locked threads show replies but disable the reply form
- [ ] Thread list is sortable by latest activity and reply count
- [ ] Threads can optionally be linked to a specific curriculum topic
- [ ] LMS admin can view all forum threads across all orgs
