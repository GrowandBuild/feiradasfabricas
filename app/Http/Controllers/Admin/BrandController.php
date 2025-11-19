<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /** Inline create a simple brand record (used by admin quick UI) */
    public function inlineCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        $name = trim($request->name);
        if ($name === '') {
            return response()->json(['success' => false, 'message' => 'Nome inválido'], 422);
        }

        // Try to infer department id if a slug or id is provided
        $departmentId = null;
        if ($request->filled('department')) {
            $maybe = $request->department;
            if (is_numeric($maybe)) {
                $departmentId = (int)$maybe;
            } else {
                $dept = \App\Models\Department::where('slug', $maybe)
                    ->orWhereRaw('LOWER(slug) = LOWER(?)', [$maybe])
                    ->orWhere('slug', Str::slug($maybe))
                    ->first();
                if ($dept) $departmentId = $dept->id;
            }
        }

        $brand = Brand::firstOrCreate([
            'name' => $name,
        ], [
            'slug' => Str::slug($name),
            'logo' => $request->logo ?: null,
            'department_id' => $departmentId,
        ]);

        return response()->json(['success' => true, 'brand' => $brand->name, 'id' => $brand->id]);
    }
}
