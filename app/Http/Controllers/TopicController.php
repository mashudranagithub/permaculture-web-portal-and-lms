<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    /**
     * Store a newly created topic in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title.en' => 'required|string|max:255',
            'title.bn' => 'required|string|max:255',
            'description.en' => 'nullable|string',
            'description.bn' => 'nullable|string',
            'content_body.en' => 'nullable|string',
            'content_body.bn' => 'nullable|string',
            'topic_type' => 'required|in:content,pdf,online_class,assignment,quiz',
            'video_url' => 'nullable|url',
            'estimated_duration' => 'nullable|string',
            'is_published' => 'boolean',
            'is_free_preview' => 'boolean',
            'pdf_file_en' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_file_bn' => 'nullable|file|mimes:pdf|max:10240',
            'audio_file_en' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_file_bn' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
        ]);

        // Auto-set order index
        $validated['order_index'] = Topic::where('course_id', $request->course_id)->count() + 1;

        // Handle File Uploads
        $this->handleFileUploads($request, $validated);

        Topic::create($validated);

        return redirect()->back()->with('message', 'Topic added successfully!');
    }

    /**
     * Update the specified topic in storage.
     */
    public function update(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'title.en' => 'required|string|max:255',
            'title.bn' => 'required|string|max:255',
            'description.en' => 'nullable|string',
            'description.bn' => 'nullable|string',
            'content_body.en' => 'nullable|string',
            'content_body.bn' => 'nullable|string',
            'topic_type' => 'required|in:content,pdf,online_class,assignment,quiz',
            'video_url' => 'nullable|url',
            'estimated_duration' => 'nullable|string',
            'is_published' => 'boolean',
            'is_free_preview' => 'boolean',
            'pdf_file_en' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_file_bn' => 'nullable|file|mimes:pdf|max:10240',
            'audio_file_en' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_file_bn' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
        ]);

        // Handle File Uploads (cleanup old ones if new ones uploaded)
        $this->handleFileUploads($request, $validated, $topic);

        $topic->update($validated);

        return redirect()->back()->with('message', 'Topic updated successfully!');
    }

    /**
     * Reorder topics.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|exists:topics,id',
            'topics.*.order_index' => 'required|integer',
        ]);

        foreach ($request->topics as $item) {
            Topic::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
        }

        return redirect()->back()->with('message', 'Curriculum reordered successfully!');
    }

    /**
     * Remove the specified topic from storage.
     */
    public function destroy(Topic $topic): RedirectResponse
    {
        // Cleanup files
        $files = ['pdf_file_en', 'pdf_file_bn', 'audio_file_en', 'audio_file_bn'];
        foreach ($files as $field) {
            if ($topic->$field) Storage::disk('public')->delete($topic->$field);
        }

        $topic->delete();

        return redirect()->back()->with('message', 'Topic removed successfully!');
    }

    /**
     * Helper to handle file uploads for topics.
     */
    private function handleFileUploads(Request $request, &$validated, ?Topic $topic = null): void
    {
        $files = ['pdf_file_en', 'pdf_file_bn', 'audio_file_en', 'audio_file_bn'];
        $courseId = $topic ? $topic->course_id : $request->course_id;

        foreach ($files as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($topic && $topic->$field) {
                    Storage::disk('public')->delete($topic->$field);
                }
                $folder = str_contains($field, 'pdf') ? 'pdfs' : 'audios';
                $validated[$field] = $request->file($field)->store("courses/{$courseId}/{$folder}", 'public');
            }
        }
    }
}
