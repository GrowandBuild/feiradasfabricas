<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::ordered()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }
    // Adicione os outros métodos necessários aqui

    /**
     * Sincroniza alterações rápidas vindas do painel flutuante
     */
    public function inlineSync(Request $request)
    {
        $validated = $request->validate([
            'departments' => 'required|array|min:1',
            'departments.*.id' => 'nullable|integer|exists:departments,id',
            'departments.*.name' => 'required|string|max:255',
            'departments.*.slug' => 'nullable|string|max:255',
            'departments.*.icon' => 'nullable|string|max:255',
            'departments.*.color' => 'nullable|string|max:20',
            'departments.*.description' => 'nullable|string',
            'departments.*.is_active' => 'boolean',
        ]);

        $results = [];

        DB::transaction(function () use (&$results, $validated) {
            foreach ($validated['departments'] as $index => $item) {
                $name = trim($item['name']);
                $id = $item['id'] ?? null;
                $color = trim($item['color'] ?? '#667eea');
                if (!str_starts_with($color, '#') || strlen($color) < 4) {
                    $color = '#667eea';
                }

                $slug = $this->ensureUniqueSlug($item['slug'] ?? $name, $id);

                $payload = [
                    'name' => $name,
                    'slug' => $slug,
                    'icon' => $item['icon'] ?? null,
                    'color' => $color,
                    'description' => $item['description'] ?? null,
                    'is_active' => array_key_exists('is_active', $item) ? (bool) $item['is_active'] : true,
                    'sort_order' => $index,
                ];

                if ($id) {
                    $department = Department::findOrFail($id);
                    $department->update($payload);
                } else {
                    $department = Department::create($payload);
                }

                $department->loadCount(['products as active_products_count' => function ($query) {
                    $query->where('is_active', true);
                }]);

                $results[] = [
                    'id' => $department->id,
                    'name' => $department->name,
                    'slug' => $department->slug,
                    'icon' => $department->icon,
                    'color' => $department->color,
                    'description' => $department->description,
                    'is_active' => $department->is_active,
                    'products_count' => (int) $department->active_products_count,
                    'sort_order' => $department->sort_order,
                    'url' => route('department.index', $department->slug),
                ];
            }
        });

        return response()->json([
            'success' => true,
            'departments' => $results,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        Department::create($data);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        $department->load('products', 'categories');
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Retorna um snapshot JSON dos departamentos para uso inline (painel flutuante)
     */
    public function inlineSnapshot()
    {
        $departments = Department::ordered()->get();

        $results = $departments->map(function ($d) {
            $d->loadCount(['products as active_products_count' => function ($q) { $q->where('is_active', true); }]);
            return [
                'id' => $d->id,
                'name' => $d->name,
                'slug' => $d->slug,
                'icon' => $d->icon,
                'color' => $d->color,
                'description' => $d->description,
                'is_active' => (bool) $d->is_active,
                'products_count' => (int) ($d->active_products_count ?? 0),
                'sort_order' => $d->sort_order,
                'url' => \Illuminate\Support\Facades\Route::has('department') ? route('department', $d->slug) : url('/departamento/' . $d->slug),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'departments' => $results,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        // Debug: verify what data is coming
        \Log::info('Department Update Request', [
            'department_id' => $department->id,
            'old_name' => $department->name,
            'new_name' => $request->input('name'),
            'all_inputs' => $request->all()
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
        ]);

        // Update department directly using fillable attributes
        $department->name = $request->input('name');
        $department->description = $request->input('description');
        $department->icon = $request->input('icon');
        $department->color = $request->input('color');
        $department->is_active = $request->has('is_active');
        $department->sort_order = $request->input('sort_order', 0);
        
        // Explicitly save the changes
        $saved = $department->save();
        
        \Log::info('Department After Save', [
            'saved' => $saved,
            'new_name' => $department->name
        ]);

        // Se veio arquivo de logo, validar e armazenar em storage/public/department-logos
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:10240'
            ]);

            $file = $request->file('logo');
            $filename = 'dept_logo_' . $department->slug . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('department-logos', $filename, 'public');

            // Salvar via Setting para manter consistência com outras chaves por departamento
            \App\Models\Setting::set('dept_' . $department->slug . '_logo', $path);
        }

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento atualizado com sucesso!');
    }

    /**
     * Salva as cores do tema do departamento
     */
    public function saveThemeColors(Request $request, Department $department)
    {
        $request->validate([
            'theme_primary' => 'required|string|max:7',
            'theme_secondary' => 'required|string|max:7',
        ]);

        $slug = $department->slug;
        \App\Models\Setting::set('dept_' . $slug . '_theme_primary', $request->input('theme_primary'));
        \App\Models\Setting::set('dept_' . $slug . '_theme_secondary', $request->input('theme_secondary'));

        return response()->json(['success' => true]);
    }

    /**
     * Restaura as cores do tema do departamento
     */
    public function restoreThemeColors(Department $department)
    {
        $slug = $department->slug;
        $primary = \App\Models\Setting::get('dept_' . $slug . '_theme_primary', '#0f172a');
        $secondary = \App\Models\Setting::get('dept_' . $slug . '_theme_secondary', '#ff6b35');
        return response()->json([
            'theme_primary' => $primary,
            'theme_secondary' => $secondary,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        // Verificar se há produtos associados
        if ($department->products()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Não é possível excluir o departamento pois há produtos associados a ele.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento excluído com sucesso!');
    }

    /**
     * Garante slugs únicos, reaproveitando o existente quando possível
     */
    protected function ensureUniqueSlug(string $incoming, ?int $ignoreId = null): string
    {
        $candidate = Str::slug($incoming);
        if ($candidate === '') {
            $candidate = Str::slug(Str::random(8));
        }

        $base = $candidate;
        $counter = 1;

        while (
            Department::where('slug', $candidate)
                ->when($ignoreId, function ($query, $ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
