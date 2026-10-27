<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    /**
     * Display the About Page Editor.
     */
    public function edit(): View
    {
        abort_unless(
            auth()->user()?->role === 'super_admin',
            403
        );

        $aboutPage = AboutPage::first();

        return view('admin.about-page.editorabout', compact('aboutPage'));
    }

    /**
     * Save the About page content.
     */
    public function update(Request $request): JsonResponse
    {
        abort_unless(
            auth()->user()?->role === 'super_admin',
            403
        );

        $validated = $request->validate([
            'html' => ['nullable', 'string', 'max:1000000'],
            'css' => ['nullable', 'string', 'max:1000000'],
            'javascript' => ['nullable', 'string', 'max:1000000'],
        ]);

        $aboutPage = AboutPage::first() ?? new AboutPage();

        $aboutPage->fill([
            'html' => $validated['html'] ?? '',
            'css' => $validated['css'] ?? '',
            'javascript' => $validated['javascript'] ?? '',
        ]);

        $aboutPage->save();

        return response()->json([
            'message' => 'About page saved successfully.',
        ]);
    }
}
