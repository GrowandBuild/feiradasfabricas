<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomepageSection;
use App\Models\Department;
use App\Models\Product;

class HomepageSectionController extends Controller
{
    public function index(Request $request)
    {
        $query = HomepageSection::orderBy('position');

        // Optional department filter via query string (id or slug)
        if ($request->has('department')) {
            $dept = $request->get('department');
            if (is_numeric($dept)) {
                $query->where('department_id', (int)$dept);
            } else {
                // try resolve slug -> id
                $d = Department::where('slug', $dept)
                    ->orWhereRaw('LOWER(slug) = LOWER(?)', [$dept])
                    ->orWhere('name', 'like', "%{$dept}%")
                    ->first();
                if ($d) $query->where('department_id', $d->id);
            }
        }

        $sections = $query->get();

        // If JSON requested, return a lightweight JSON payload for the admin widgets
        if ($request->wantsJson() || $request->get('as') === 'json') {
            $payload = $sections->map(function($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'department_id' => $s->department_id,
                    'product_ids' => $s->product_ids ?? [],
                    'limit' => $s->limit,
                    'position' => $s->position,
                    'enabled' => (bool)($s->enabled ?? false),
                ];
            })->toArray();

            return response()->json(['success' => true, 'sections' => $payload]);
        }

        return view('admin.homepage_sections.index', compact('sections'));
    }

    public function create()
    {
        $section = new HomepageSection();
        $departments = Department::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(500)->get();
        return view('admin.homepage_sections.form', compact('section', 'departments', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'limit' => 'nullable|integer|min:1|max:50',
            'position' => 'nullable|integer',
            // don't validate as boolean (checkbox posts 'on'), convert explicitly below
            'enabled' => 'sometimes',
        ]);

        // Ensure enabled is a proper boolean (handles on/1/true values reliably)
        $data['enabled'] = $request->boolean('enabled');
        $section = HomepageSection::create($data);

        return redirect()->route('admin.homepage-sections.index')->with('success', 'Sessão criada.');
    }

    public function edit(HomepageSection $homepageSection)
    {
        $section = $homepageSection;
        $departments = Department::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(500)->get();
        return view('admin.homepage_sections.form', compact('section', 'departments', 'products'));
    }

    public function update(Request $request, HomepageSection $homepageSection)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'limit' => 'nullable|integer|min:1|max:50',
            'position' => 'nullable|integer',
            // don't validate as boolean (checkbox posts 'on'), convert explicitly below
            'enabled' => 'sometimes',
        ]);

        $data['enabled'] = $request->boolean('enabled');
        $homepageSection->update($data);

        return redirect()->route('admin.homepage-sections.index')->with('success', 'Sessão atualizada.');
    }

    public function destroy(HomepageSection $homepageSection)
    {
        $homepageSection->delete();
        return redirect()->route('admin.homepage-sections.index')->with('success', 'Sessão removida.');
    }
}
