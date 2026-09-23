<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LegalPageController extends Controller
{
    public function edit(string $type)
    {
        $legalPage = LegalPage::where('type', $type)->firstOrFail();

        Gate::authorize('update', $legalPage);

        return view('Admin.legal-pages.edit', compact('legalPage'));
    }

    public function update(Request $request, string $type)
    {
        $legalPage = LegalPage::where('type', $type)->firstOrFail();

        Gate::authorize('update', $legalPage);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $legalPage->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'updated_by' => $request->user()->id,
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Legal page updated successfully.',
        ]);
    }
}