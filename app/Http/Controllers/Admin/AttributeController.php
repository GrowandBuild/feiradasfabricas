<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Services\VariationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    protected $variationService;

    public function __construct(VariationService $variationService)
    {
        $this->variationService = $variationService;
    }

    /**
     * Lista todos os atributos
     */
    public function index()
    {
        $attributes = ProductAttribute::withCount('values')
                                     ->orderBy('sort_order')
                                     ->orderBy('name')
                                     ->paginate(20);
        
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Retorna lista de atributos em JSON
     */
    public function list()
    {
        $attributes = ProductAttribute::with(['values' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        return response()->json([
            'success' => true,
            'attributes' => $attributes->map(function($attr) {
                return [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'type' => $attr->type,
                    'values' => $attr->values->map(function($val) {
                        return [
                            'id' => $val->id,
                            'value' => $val->value,
                            'display_value' => $val->display_value ?: $val->value,
                            'color_hex' => $val->color_hex,
                            'image_url' => $val->image_url
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Mostra formulário de criação
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Cria novo atributo
     */
    public function store(Request $request)
    {
        // Log para debug
        \Log::info('Tentando criar atributo', [
            'request_data' => $request->all(),
            'values_count' => count($request->values ?? [])
        ]);

        $rules = [
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'type' => 'required|in:color,size,text,number,image',
            'is_active' => 'nullable|in:on,1,true', // Aceita 'on' do checkbox
            'sort_order' => 'nullable|integer|min:0',
            'values' => 'required|array|min:1',
            'values.*.value' => 'required|string|max:255',
            'values.*.display_value' => 'nullable|string|max:255', // Opcional - se vazio, usa 'value'
            'values.*.sort_order' => 'nullable|integer|min:0',
        ];

        // Se tipo for color, color_hex é obrigatório
        if ($request->type === 'color') {
            $rules['values.*.color_hex'] = 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
        } else {
            $rules['values.*.color_hex'] = 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
        }

        // Se tipo for image, image_url pode ser opcional
        if ($request->type === 'image') {
            $rules['values.*.image_url'] = 'nullable|url|max:500';
        } else {
            $rules['values.*.image_url'] = 'nullable|url|max:500';
        }

        try {
            $validated = $request->validate($rules);
            \Log::info('Validação passou', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            throw $e;
        }

        $attribute = ProductAttribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Criar valores do atributo
        foreach ($request->values as $index => $valueData) {
            // Log para debug
            \Log::info('Criando valor do atributo', [
                'attribute_id' => $attribute->id,
                'index' => $index,
                'value_data' => $valueData
            ]);
            
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => $valueData['value'],
                'slug' => Str::slug($valueData['value']),
                'display_value' => !empty($valueData['display_value']) ? $valueData['display_value'] : $valueData['value'],
                'color_hex' => $valueData['color_hex'] ?? null,
                'image_url' => $valueData['image_url'] ?? null,
                'sort_order' => $valueData['sort_order'] ?? $index,
                'is_active' => true,
            ]);
        }

        // Limpar cache
        $this->variationService->clearAttributesCache();

        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Atributo criado com sucesso!');
    }

    /**
     * Mostra detalhes do atributo
     */
    public function show(ProductAttribute $attribute)
    {
        $attribute->load('allValues');
        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('allValues');
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Atualiza atributo
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $attribute->id,
            'type' => 'required|in:color,size,text,number,image',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? $attribute->sort_order,
        ]);

        // Limpar cache
        $this->variationService->clearAttributesCache();

        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Atributo atualizado com sucesso!');
    }

    /**
     * Remove atributo
     */
    public function destroy(ProductAttribute $attribute)
    {
        // Verificar se está sendo usado em variações
        if ($attribute->variations()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Não é possível excluir um atributo que está sendo usado em variações de produtos.');
        }

        // Deletar valores do atributo
        $attribute->allValues()->delete();
        
        // Deletar atributo
        $attribute->delete();

        // Limpar cache
        $this->variationService->clearAttributesCache();

        return redirect()->route('admin.attributes.index')
                        ->with('success', 'Atributo excluído com sucesso!');
    }

    /**
     * API: Busca um valor de atributo
     */
    public function getValue(AttributeValue $value)
    {
        $value->load('attribute');
        
        return response()->json([
            'success' => true,
            'value' => [
                'id' => $value->id,
                'attribute_id' => $value->attribute_id,
                'value' => $value->value,
                'display_value' => $value->display_value,
                'color_hex' => $value->color_hex,
                'image_url' => $value->image_url,
                'sort_order' => $value->sort_order,
                'is_active' => $value->is_active,
                'attribute' => [
                    'id' => $value->attribute->id,
                    'name' => $value->attribute->name,
                    'type' => $value->attribute->type
                ]
            ]
        ]);
    }

    /**
     * API: Adiciona valor a um atributo
     */
    public function addValue(Request $request, ProductAttribute $attribute)
    {
        try {
            $rules = [
                'value' => 'required|string|max:255',
                'display_value' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
            ];

            // Validação condicional para color_hex
            if ($attribute->type === 'color') {
                $rules['color_hex'] = 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
            } else {
                $rules['color_hex'] = 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
            }

            // Validação para image_url
            if ($attribute->type === 'image') {
                $rules['image_url'] = 'nullable|url|max:500';
            } else {
                $rules['image_url'] = 'nullable|url|max:500';
            }

            $validated = $request->validate($rules);

            $value = AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => $request->value,
                'slug' => Str::slug($request->value),
                'display_value' => $request->display_value ?? $request->value,
                'color_hex' => $request->color_hex ?? null,
                'image_url' => $request->image_url ?? null,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true,
            ]);

            // Limpar cache
            $this->variationService->clearAttributesCache();

            return response()->json([
                'success' => true,
                'message' => 'Valor adicionado com sucesso!',
                'value' => $value->load('attribute')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Atualiza valor de atributo
     */
    public function updateValue(Request $request, AttributeValue $value)
    {
        try {
            $rules = [
                'value' => 'required|string|max:255',
                'display_value' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ];

            // Validação condicional para color_hex
            if ($value->attribute->type === 'color') {
                $rules['color_hex'] = 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
            } else {
                $rules['color_hex'] = 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/';
            }

            // Validação para image_url
            $rules['image_url'] = 'nullable|url|max:500';

            $validated = $request->validate($rules);

            $value->update([
                'value' => $request->value,
                'slug' => Str::slug($request->value),
                'display_value' => $request->display_value ?? $request->value,
                'color_hex' => $request->color_hex ?? $value->color_hex,
                'image_url' => $request->image_url ?? $value->image_url,
                'sort_order' => $request->sort_order ?? $value->sort_order,
                'is_active' => $request->boolean('is_active', $value->is_active),
            ]);

            // Limpar cache
            $this->variationService->clearAttributesCache();

            return response()->json([
                'success' => true,
                'message' => 'Valor atualizado com sucesso!',
                'value' => $value->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Remove valor de atributo
     */
    public function destroyValue(AttributeValue $value)
    {
        // Verificar se está sendo usado em variações
        if ($value->variations()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um valor que está sendo usado em variações de produtos.'
            ], 400);
        }

        $value->delete();

        // Limpar cache
        $this->variationService->clearAttributesCache();

        return response()->json([
            'success' => true
        ]);
    }
}

