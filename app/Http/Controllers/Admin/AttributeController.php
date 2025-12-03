<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Department;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('department', 'values')->orderBy('sort_order')->paginate(25);
        $departments = Department::orderBy('name')->get();
        return view('admin.attributes.index', compact('attributes','departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.attributes.create', compact('departments'));
    }

    public function store(Request $request)
    {
        \Log::info('AttributeController@store called', ['input' => $request->all(), 'ip' => $request->ip()]);

        try {
            $data = $request->validate([
            'name' => 'required|string|max:191',
            'key' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('AttributeController@store validation failed', ['errors' => $e->errors(), 'input' => $request->all()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            \Log::error('AttributeController@store exception before create', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Erro ao tentar criar atributo. Veja logs para mais detalhes.')->withInput();
        }

        $data['is_active'] = $request->has('is_active');
        $attr = null;
        try {
            $attr = Attribute::create($data);
            \Log::info('AttributeController@store created attribute', ['id' => $attr->id, 'input' => $data]);
        } catch (\Throwable $e) {
            \Log::error('AttributeController@store error creating attribute', ['message' => $e->getMessage(), 'input' => $data, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Erro ao salvar atributo. Veja logs.')->withInput();
        }

        return redirect()->route('admin.attributes.index')->with('success','Atributo criado com sucesso');
    }

    public function edit(Attribute $attribute)
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.attributes.edit', compact('attribute','departments'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'key' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $attribute->update($data);
        return redirect()->route('admin.attributes.index')->with('success','Atributo atualizado');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success','Atributo excluído');
    }

    public function show(Attribute $attribute)
    {
        $attribute->load('values');
        return view('admin.attributes.show', compact('attribute'));
    }

    // Value management
    public function storeValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'value' => 'required|string|max:191',
            'hex' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $data['attribute_id'] = $attribute->id;
        AttributeValue::create($data);
        return back()->with('success','Valor adicionado');
    }

    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $data = $request->validate([
            'value' => 'required|string|max:191',
            'hex' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $value->update($data);
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'value' => $value->fresh()
            ]);
        }
        return back()->with('success','Valor atualizado');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $value->delete();
        return back()->with('success','Valor excluído');
    }
}
