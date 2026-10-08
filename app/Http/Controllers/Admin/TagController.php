<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
        ]);

        // Auto-generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        $tag = Tag::create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tag created successfully.',
                'tag' => $tag->load(['articles' => function($q) { $q->where('status', 'published'); }])
                    ->makeVisible('articles_count')
            ]);
        }

        return redirect()
            ->route('admin.dashboard', ['tab' => 'taxonomy'])
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name,' . $tag->id],
        ]);

        // Auto-generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        $tag->update($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully.',
                'tag' => $tag
            ]);
        }

        return redirect()
            ->route('admin.dashboard', ['tab' => 'taxonomy'])
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag)
    {
        $articlesCount = $tag->articles()->count();
        $tag->delete();

        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tag deleted successfully. Removed from {$articlesCount} article(s)."
            ]);
        }

        return redirect()
            ->route('admin.dashboard', ['tab' => 'taxonomy'])
            ->with('success', "Tag deleted successfully. Removed from {$articlesCount} article(s).");
    }
}
