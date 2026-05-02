# Module: Course Management
**Phase:** 0 (Foundation)  
**Status:** ✅ Completed  

---

## 1. Overview
The Course Management module allows administrators to create and manage the educational catalog. It supports bilingual content and multiple delivery modes.

---

## 2. Course Schema
| Field | Type | Description |
|---|---|---|
| `title` | JSON | Bilingual title (EN/BN) |
| `slug` | String | Unique URL identifier |
| `description` | JSON | Full bilingual course description |
| `image` | String | Path to thumbnail image |
| `price` | Decimal | Base price for the course |
| `level` | Enum | Foundation, Intermediate, Advanced |
| `delivery_mode`| Enum | Online, Offline, Hybrid |
| `status` | Enum | Draft, Published, Archived |

---

## 3. Key Features
- **Bilingual Entry**: Side-by-side or tabbed input for English and Bangla.
- **Slug Generation**: Automatic URL-friendly slug creation.
- **Media Handling**: Image uploads for thumbnails and banners.
- **Filtering & Search**: Searchable course list with status and level filters.
- **Soft Deletes**: Courses can be archived or deleted without data loss.

---

## 4. Business Logic
- **Lifecycle**: Courses start as `Draft` (hidden), move to `Published` (catalog), and eventually `Archived`.
- **Tenancy**: Now scoped to an `organization_id` for multi-tenant isolation.
- **Translations**: Powered by the `HasTranslations` trait.

---

## 5. File References
- **Model**: `app/Models/Course.php`
- **Controller**: `app/Http/Controllers/CourseController.php`
- **Vue Pages**: `resources/js/Pages/Courses/Index.vue`, `resources/js/Pages/Courses/Create.vue`
