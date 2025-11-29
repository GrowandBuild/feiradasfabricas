<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomepageSection;
use App\Models\Department;
use App\Models\Product;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::orderBy('position')->get();
        return view('admin.homepage_sections.index', compact('sections'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(500)->get();
        return view('admin.homepage_sections.form', ['section' => new HomepageSection(), 'departments' => $departments, 'products' => $products]);
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
