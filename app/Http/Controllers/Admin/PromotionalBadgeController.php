<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionalBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionalBadgeController extends Controller
{
    public function index()
    {
        $badges = PromotionalBadge::orderBy('created_at', 'desc')->get();
        return view('admin.promotional-badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.promotional-badges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url|max:255',
            'position' => 'required|in:bottom-right,bottom-left,center-bottom,top-right,top-left,center-top,center',
            'auto_close_seconds' => 'nullable|integer|min:0|max:300',
            'show_close_button' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('promotional-badges', 'public');
        }

        $validated['show_close_button'] = $request->has('show_close_button');
        $validated['is_active'] = $request->has('is_active');
        $validated['auto_close_seconds'] = $validated['auto_close_seconds'] ?? 0;

        PromotionalBadge::create($validated);

        return redirect()->route('admin.promotional-badges.index')
            ->with('success', 'Badge criado com sucesso!');
    }

    public function edit(PromotionalBadge $promotionalBadge)
    {
        return view('admin.promotional-badges.edit', compact('promotionalBadge'));
    }

    public function update(Request $request, PromotionalBadge $promotionalBadge)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url|max:255',
            'position' => 'required|in:bottom-right,bottom-left,center-bottom,top-right,top-left,center-top,center',
            'auto_close_seconds' => 'nullable|integer|min:0|max:300',
            'show_close_button' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($promotionalBadge->image && Storage::disk('public')->exists($promotionalBadge->image)) {
                Storage::disk('public')->delete($promotionalBadge->image);
            }
            $validated['image'] = $request->file('image')->store('promotional-badges', 'public');
        }

        $validated['show_close_button'] = $request->has('show_close_button');
        $validated['is_active'] = $request->has('is_active');
        $validated['auto_close_seconds'] = $validated['auto_close_seconds'] ?? 0;

        $promotionalBadge->update($validated);

        return redirect()->route('admin.promotional-badges.index')
            ->with('success', 'Badge atualizado com sucesso!');
    }

    public function destroy(PromotionalBadge $promotionalBadge)
    {
        if ($promotionalBadge->image && Storage::disk('public')->exists($promotionalBadge->image)) {
            Storage::disk('public')->delete($promotionalBadge->image);
        }

        $promotionalBadge->delete();

        return redirect()->route('admin.promotional-badges.index')
            ->with('success', 'Badge removido com sucesso!');
    }
}
