<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PhysicalSale;
use App\Services\PhysicalStoreService;
use App\Services\POSPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PDVController extends Controller
{
    protected PhysicalStoreService $physicalStoreService;

    public function __construct(PhysicalStoreService $physicalStoreService)
    {
        $this->physicalStoreService = $physicalStoreService;
    }

    /**
     * Exibe a interface do PDV
     */
    public function index()
    {
        // Verificar se está ativado
        if (!$this->physicalStoreService->isEnabled()) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Loja física não está ativada. Ative em Configurações > Loja Física.');
        }

        return view('admin.pdv.index');
    }

    /**
     * Buscar produtos para o PDV
     */
    public function searchProducts(Request $request)
    {
        if (!$this->physicalStoreService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Loja física não está ativada',
            ], 403);
        }

        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $products = $this->physicalStoreService->searchProducts($query);

        return response()->json([
            'success' => true,
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'b2b_price' => $product->b2b_price,
                    'stock_quantity' => $product->stock_quantity,
                    'available_stock' => app(\App\Services\InventoryService::class)->getAvailableStock($product),
                    'image' => $product->first_image,
                    'has_variations' => $product->has_variations,
                    'variations' => $product->variations->map(function($variation) {
                        return [
                            'id' => $variation->id,
                            'name' => $variation->name,
                            'sku' => $variation->sku,
                            'price' => $variation->price,
                            'stock_quantity' => $variation->stock_quantity,
                            'in_stock' => $variation->in_stock,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Criar uma venda física
     */
    public function createSale(Request $request)
    {
        if (!$this->physicalStoreService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Loja física não está ativada',
            ], 403);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:dinheiro,cartao_debito,cartao_credito,pix,cheque',
            'installments' => 'nullable|integer|min:1|max:12',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Criar a venda
            $sale = $this->physicalStoreService->createSale($validated);

            // Processar pagamento (modo manual)
            $posPaymentService = app(POSPaymentService::class);
            $paymentResult = $posPaymentService->processPayment($sale, [
                'payment_method' => $validated['payment_method'],
                'installments' => $validated['installments'] ?? 1,
            ]);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['error'] ?? 'Erro ao processar pagamento');
            }

            // Se requer confirmação (cartão no modo manual), retornar instruções
            if ($paymentResult['requires_confirmation'] ?? false) {
                return response()->json([
                    'success' => true,
                    'requires_confirmation' => true,
                    'message' => $paymentResult['message'],
                    'instructions' => $posPaymentService->getPaymentInstructions(
                        $validated['payment_method'],
                        'manual'
                    ),
                    'sale' => [
                        'id' => $sale->id,
                        'sale_number' => $sale->sale_number,
                        'total' => $sale->total,
                        'payment_status' => $sale->payment_status,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'sale' => [
                    'id' => $sale->id,
                    'sale_number' => $sale->sale_number,
                    'total' => $sale->total,
                    'payment_status' => $sale->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar venda física: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obter detalhes de um produto
     */
    public function getProduct($id)
    {
        if (!$this->physicalStoreService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Loja física não está ativada',
            ], 403);
        }

        $product = Product::with(['variations' => function($q) {
            $q->where('in_stock', true);
        }])->findOrFail($id);

        $inventoryService = app(\App\Services\InventoryService::class);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'b2b_price' => $product->b2b_price,
                'stock_quantity' => $product->stock_quantity,
                'available_stock' => $inventoryService->getAvailableStock($product),
                'image' => $product->first_image,
                'has_variations' => $product->has_variations,
                'variations' => $product->variations->map(function($variation) {
                    return [
                        'id' => $variation->id,
                        'name' => $variation->name,
                        'sku' => $variation->sku,
                        'price' => $variation->price,
                        'stock_quantity' => $variation->stock_quantity,
                        'in_stock' => $variation->in_stock,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Confirmar pagamento manual (após processar na maquininha)
     */
    public function confirmPayment(Request $request, PhysicalSale $sale)
    {
        if (!$this->physicalStoreService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Loja física não está ativada',
            ], 403);
        }

        $validated = $request->validate([
            'confirmed' => 'required|boolean',
            'reference' => 'nullable|string|max:100', // NSU, código de autorização, etc.
        ]);

        if (!$validated['confirmed']) {
            return response()->json([
                'success' => false,
                'message' => 'Confirmação não autorizada',
            ], 400);
        }

        try {
            $posPaymentService = app(POSPaymentService::class);
            $result = $posPaymentService->confirmManualPayment($sale, $validated);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'sale' => $sale->fresh(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Erro ao confirmar pagamento',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar pagamento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar pagamento: ' . $e->getMessage(),
            ], 500);
        }
    }
}
