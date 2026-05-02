# Module: Curriculum & Topic Builder
**Phase:** 0 (Foundation)  
**Status:** ✅ Completed  

---

## 1. Overview
The Curriculum Builder is a high-end interface for structuring course content. It allows administrators to create sequential learning paths using various content types.

---

## 2. Topic Types
| Type | Description |
|---|---|
| **Content** | Rich HTML body (Text, Images, Embeds). |
| **PDF** | Integrated PDF viewer (separate EN/BN files). |
| **Video** | YouTube/Vimeo embeds. |
| **Audio** | MP3/WAV uploads with an integrated player. |
| **Online Class** | Scheduled live sessions (Zoom/Meet) + recording fallback. |
| **Quiz** | Interactive assessments (stored as JSON `quiz_data`). |

---

## 3. Advanced Features
- **Drag-and-Drop Reordering**: Interactive UI for setting the learning sequence.
- **Bilingual Media**: Separate fields for English and Bangla audio/PDF files.
- **Rich Text Editor**: Integrated WYSIWYG for complex content body.
- **Free Preview**: Toggle individual topics to be accessible without enrollment.
- **Quiz Builder**: Dynamic question/option builder within the topic form.

---

## 4. Technical Implementation
- **Storage**: Media files are stored in `storage/app/public/courses/{id}/{type}/`.
- **Validation**: Enforces file size limits (5MB for Audio, 10MB for PDF).
- **JSON Fields**: `title`, `description`, `content_body`, and `quiz_data` are all JSON-casted.

---

## 5. File References
- **Model**: `app/Models/Topic.php`
- **Controller**: `app/Http/Controllers/TopicController.php`
- **Vue Pages**: `resources/js/Pages/Courses/Curriculum.vue`
