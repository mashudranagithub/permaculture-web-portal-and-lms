<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Handle image uploads from Rich Text Editors.
     */
    public function upload(Request $request): JsonResponse
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            
            // Validate the file
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Store the file in a public media directory
            $path = $file->store('media/ckeditor', 'public');
            
            // Return the URL for CKEditor
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'No file uploaded or file invalid.'
            ]
        ], 400);
    }
}
