<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DepartmentSection;
use Illuminate\Support\Str;

class DepartmentSectionController extends Controller
{
    /**
     * Return sections for a department
     */
    public function index($department)
    {
        // Resolve department by id, slug or name
        $dept = null;
        if ($department instanceof Department) {
            $dept = $department;
        } else {
            $maybeId = is_numeric($department) ? (int)$department : null;
            if ($maybeId) {
                $dept = Department::find($maybeId);
            }
            if (!$dept) {
                $dept = Department::where('slug', $department)
                    ->orWhereRaw('LOWER(slug) = LOWER(?)', [$department])
                    ->orWhere('slug', Str::slug($department))
                    ->orWhere('name', 'like', "%{$department}%")
                    ->first();
            }
        }

        if (!$dept) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        $sections = DepartmentSection::where('department_id', $dept->id)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'type' => $s->type,
                    'reference' => $s->reference,
                    'reference_id' => $s->reference_id,
                    'title' => $s->title,
                    'enabled' => (bool)$s->enabled,
                    'sort_order' => (int)$s->sort_order,
                    'metadata' => $s->metadata ?? [],
                ];
            });

        return response()->json(['success' => true, 'sections' => $sections]);
    }

    /**
     * Sync whole set of sections for a department (overwrite)
     */
    public function sync(Request $request, $department)
    {
        $data = $request->validate([
            'sections' => 'required|array',
            'sections.*.type' => 'required|in:brand,category,tag,dynamic',
            'sections.*.reference' => 'nullable|string|max:255',
            'sections.*.reference_id' => 'nullable|integer',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.enabled' => 'nullable|boolean',
            'sections.*.sort_order' => 'nullable|integer',
            'sections.*.metadata' => 'nullable|array',
        ]);

        $incoming = $data['sections'];

        // resolve department similar to index
        $dept = null;
        if ($department instanceof Department) {
            $dept = $department;
        } else {
            $maybeId = is_numeric($department) ? (int)$department : null;
            if ($maybeId) {
                $dept = Department::find($maybeId);
            }
            if (!$dept) {
                $dept = Department::where('slug', $department)
                    ->orWhereRaw('LOWER(slug) = LOWER(?)', [$department])
                    ->orWhere('slug', Str::slug($department))
                    ->orWhere('name', 'like', "%{$department}%")
                    ->first();
            }
        }

        if (!$dept) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        // For simplicity, delete existing and recreate (safe and simple sync)
        DepartmentSection::where('department_id', $dept->id)->delete();

        $created = [];
        foreach ($incoming as $idx => $item) {
            $createdItem = DepartmentSection::create([
                'department_id' => $dept->id,
                'type' => $item['type'],
                'reference' => $item['reference'] ?? null,
                'reference_id' => $item['reference_id'] ?? null,
                'title' => $item['title'] ?? ($item['reference'] ?? null),
                'enabled' => isset($item['enabled']) ? (bool)$item['enabled'] : true,
                'sort_order' => $item['sort_order'] ?? $idx,
                'metadata' => $item['metadata'] ?? null,
            ]);

            $created[] = [
                'id' => $createdItem->id,
                'type' => $createdItem->type,
                'reference' => $createdItem->reference,
                'reference_id' => $createdItem->reference_id,
                'title' => $createdItem->title,
                'enabled' => (bool)$createdItem->enabled,
                'sort_order' => (int)$createdItem->sort_order,
            ];
        }

        return response()->json(['success' => true, 'sections' => $created]);
    }
}
