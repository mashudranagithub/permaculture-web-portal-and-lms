# Module: Batch Management
**Phase:** 0 (Foundation)  
**Status:** ✅ Completed  

---

## 1. Overview
Batches manage course cohorts. Each course can have multiple batches (e.g., Spring 2026, Autumn 2026) with independent pricing, capacity, and schedules.

---

## 2. Batch Schema
| Field | Type | Description |
|---|---|---|
| `title` | JSON | Bilingual batch title |
| `course_id` | FK | Associated course |
| `start_date` | Date | When classes begin |
| `end_date` | Date | When the batch finishes |
| `capacity` | Integer | Maximum seats (0 for unlimited) |
| `status` | Enum | Upcoming, Ongoing, Completed, Cancelled |
| `price` | Decimal | Batch-specific pricing (overrides course) |

---

## 3. Key Features
- **Cohorting**: Grouping students by time period.
- **Seat Control**: Automatic available seat calculation.
- **Enrollment Deadline**: Optional cutoff date for registration.
- **Batch Status**: Moving batches through their lifecycle (Upcoming -> Ongoing -> Completed).
- **Organization Scoping**: Batches are isolated by `organization_id`.

---

## 4. Business Logic
- **Pricing**: If a batch price is set, it overrides the global course price for that cohort.
- **Availability**: Enrollment is blocked if capacity is reached or status is not "Upcoming/Ongoing".

---

## 5. File References
- **Model**: `app/Models/Batch.php`
- **Controller**: `app/Http/Controllers/BatchController.php`
- **Vue Pages**: `resources/js/Pages/Batches/`
