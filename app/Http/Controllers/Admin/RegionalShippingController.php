<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegionalShipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegionalShippingController extends Controller
{
    /**
     * Lista todas as entregas regionais
     */
    public function index()
    {
        $regions = RegionalShipping::ordered()->get();
        return view('admin.regional-shipping.index', compact('regions'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create()
    {
        return view('admin.regional-shipping.create');
    }

    /**
     * Salva nova entrega regional
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cep_start' => 'nullable|string|size:8',
            'cep_end' => 'nullable|string|size:8',
            'cep_list' => 'nullable|string',
            'pricing_type' => 'required|in:fixed,per_weight,per_item',
            'fixed_price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_per_item' => 'nullable|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'delivery_days_min' => 'required|integer|min:1|max:60',
            'delivery_days_max' => 'nullable|integer|min:1|max:60|gte:delivery_days_min',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Normalizar CEPs (apenas dígitos)
        if (!empty($validated['cep_start'])) {
            $validated['cep_start'] = preg_replace('/\D+/', '', $validated['cep_start']);
        }
        if (!empty($validated['cep_end'])) {
            $validated['cep_end'] = preg_replace('/\D+/', '', $validated['cep_end']);
        }

        // Processar lista de CEPs
        if (!empty($validated['cep_list'])) {
            $cepList = array_map(function($cep) {
                return preg_replace('/\D+/', '', trim($cep));
            }, explode(',', $validated['cep_list']));
            $cepList = array_filter($cepList, function($cep) {
                return strlen($cep) === 8;
            });
            $validated['cep_list'] = !empty($cepList) ? json_encode(array_values($cepList)) : null;
        }

        $validated['is_active'] = $request->has('is_active');

        try {
            RegionalShipping::create($validated);
            return redirect()->route('admin.regional-shipping.index')
                ->with('success', 'Entrega regional criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar entrega regional: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Erro ao criar entrega regional: ' . $e->getMessage());
        }
    }

    /**
     * Mostra formulário de edição
     */
    public function edit($id)
    {
        $region = RegionalShipping::findOrFail($id);
        
        // Decodificar lista de CEPs para exibição
        if (!empty($region->cep_list)) {
            $cepList = json_decode($region->cep_list, true);
            if (is_array($cepList)) {
                $region->cep_list_display = implode(', ', $cepList);
            }
        }
        
        return view('admin.regional-shipping.edit', compact('region'));
    }

    /**
     * Atualiza entrega regional
     */
    public function update(Request $request, $id)
    {
        $region = RegionalShipping::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cep_start' => 'nullable|string|size:8',
            'cep_end' => 'nullable|string|size:8',
            'cep_list' => 'nullable|string',
            'pricing_type' => 'required|in:fixed,per_weight,per_item',
            'fixed_price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_per_item' => 'nullable|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'delivery_days_min' => 'required|integer|min:1|max:60',
            'delivery_days_max' => 'nullable|integer|min:1|max:60|gte:delivery_days_min',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Normalizar CEPs
        if (!empty($validated['cep_start'])) {
            $validated['cep_start'] = preg_replace('/\D+/', '', $validated['cep_start']);
        }
        if (!empty($validated['cep_end'])) {
            $validated['cep_end'] = preg_replace('/\D+/', '', $validated['cep_end']);
        }

        // Processar lista de CEPs
        if (!empty($validated['cep_list'])) {
            $cepList = array_map(function($cep) {
                return preg_replace('/\D+/', '', trim($cep));
            }, explode(',', $validated['cep_list']));
            $cepList = array_filter($cepList, function($cep) {
                return strlen($cep) === 8;
            });
            $validated['cep_list'] = !empty($cepList) ? json_encode(array_values($cepList)) : null;
        }

        $validated['is_active'] = $request->has('is_active');

        try {
            $region->update($validated);
            return redirect()->route('admin.regional-shipping.index')
                ->with('success', 'Entrega regional atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar entrega regional: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Erro ao atualizar entrega regional: ' . $e->getMessage());
        }
    }

    /**
     * Remove entrega regional
     */
    public function destroy($id)
    {
        try {
            $region = RegionalShipping::findOrFail($id);
            $region->delete();
            return redirect()->route('admin.regional-shipping.index')
                ->with('success', 'Entrega regional removida com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover entrega regional: ' . $e->getMessage());
            return back()->with('error', 'Erro ao remover entrega regional: ' . $e->getMessage());
        }
    }

    /**
     * Alterna status ativo/inativo
     */
    public function toggleActive($regionalShipping)
    {
        try {
            $region = is_numeric($regionalShipping) 
                ? RegionalShipping::findOrFail($regionalShipping)
                : $regionalShipping;
            $region->is_active = !$region->is_active;
            $region->save();
            return response()->json([
                'success' => true,
                'is_active' => $region->is_active,
                'message' => $region->is_active ? 'Região ativada' : 'Região desativada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importa múltiplas regiões de uma lista com preços individuais
     */
    public function import(Request $request)
    {
        try {
            $validated = $request->validate([
                'regions_data' => 'nullable|string', // JSON com dados das regiões
                'regions_list' => 'nullable|string', // Fallback: lista simples
                'pricing_type' => 'required|in:fixed,per_weight,per_item',
                'default_price' => 'nullable|numeric|min:0',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'delivery_days_min' => 'required|integer|min:1|max:60',
                'delivery_days_max' => 'nullable|integer|min:1|max:60|gte:delivery_days_min',
                'sort_order' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'is_active' => 'nullable',
            ]);

            $regions = [];
            
            // Se há dados JSON (da tabela de preview), usar eles
            if (!empty($validated['regions_data'])) {
                $regionsData = json_decode($validated['regions_data'], true);
                if (is_array($regionsData)) {
                    foreach ($regionsData as $region) {
                        if (!empty($region['name']) && isset($region['price']) && $region['price'] > 0) {
                            $regions[] = [
                                'name' => trim($region['name']),
                                'price' => (float) $region['price']
                            ];
                        }
                    }
                }
            } else {
                // Fallback: processar lista simples
                $lines = explode("\n", $validated['regions_list'] ?? '');
                $defaultPrice = (float) ($validated['default_price'] ?? 0);
                
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (empty($trimmed)) continue;
                    
                    // Verificar se tem preço no formato "Nome|Preço"
                    $parts = explode('|', $trimmed);
                    $name = trim($parts[0]);
                    $price = $defaultPrice;
                    
                    if (count($parts) > 1) {
                        $priceStr = trim($parts[1]);
                        $priceStr = preg_replace('/[^\d,.-]/', '', $priceStr);
                        $priceStr = str_replace(',', '.', $priceStr);
                        $parsedPrice = (float) $priceStr;
                        if ($parsedPrice > 0) {
                            $price = $parsedPrice;
                        }
                    }
                    
                    if (!empty($name)) {
                        $regions[] = [
                            'name' => $name,
                            'price' => $price
                        ];
                    }
                }
            }

            if (empty($regions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma região válida encontrada. Verifique a lista e os preços.'
                ], 422);
            }

            // Preparar dados base para todas as regiões
            $baseData = [
                'pricing_type' => $validated['pricing_type'],
                'min_price' => $validated['min_price'] ?? null,
                'max_price' => $validated['max_price'] ?? null,
                'delivery_days_min' => $validated['delivery_days_min'],
                'delivery_days_max' => $validated['delivery_days_max'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ];

            // Criar regiões com preços individuais
            $created = 0;
            $errors = [];

            foreach ($regions as $regionData) {
                $regionName = $regionData['name'];
                $regionPrice = $regionData['price'];

                try {
                    // Verificar se já existe uma região com o mesmo nome
                    $exists = RegionalShipping::where('name', $regionName)->exists();
                    if ($exists) {
                        $errors[] = "Região '{$regionName}' já existe e foi ignorada.";
                        continue;
                    }

                    // Aplicar preço conforme o tipo de precificação
                    $regionDataToSave = array_merge($baseData, [
                        'name' => $regionName,
                    ]);

                    if ($validated['pricing_type'] === 'fixed') {
                        $regionDataToSave['fixed_price'] = $regionPrice;
                    } elseif ($validated['pricing_type'] === 'per_weight') {
                        $regionDataToSave['price_per_kg'] = $regionPrice;
                    } else {
                        $regionDataToSave['price_per_item'] = $regionPrice;
                    }

                    RegionalShipping::create($regionDataToSave);
                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Erro ao criar região '{$regionName}': " . $e->getMessage();
                    Log::error("Erro ao importar região '{$regionName}': " . $e->getMessage());
                }
            }

            if ($created === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma região foi criada. ' . implode(' ', $errors)
                ], 422);
            }

            $message = "{$created} região(ões) criada(s) com sucesso!";
            if (!empty($errors)) {
                $message .= " Avisos: " . implode(' ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " e mais " . (count($errors) - 5) . " aviso(s).";
                }
            }

            return response()->json([
                'success' => true,
                'created' => $created,
                'message' => $message,
                'errors' => $errors
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => $ve->validator->errors()->first(),
                'errors' => $ve->validator->errors()->toArray(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao importar regiões: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar regiões: ' . $e->getMessage()
            ], 500);
        }
    }
}
