<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Customer;
use App\Events\CartUpdated;
use App\Services\VariationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Exibe o carrinho
     */
    public function index()
    {
        // Garantir que o session_id está definido e é único para esta sessão
        $this->getSessionId();
        
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        
        // Calcular frete da sessão
        $shipping = $this->calculateShipping();
        $total = $subtotal + $shipping;
        
        return view('cart.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }
    
    /**
     * Calcula o frete baseado na seleção da sessão
     */
    private function calculateShipping()
    {
        $selection = Session::get('shipping_selection');
        if (is_array($selection) && isset($selection['price'])) {
            return (float) $selection['price'];
        }
        return 0.0;
    }

    /**
     * Adiciona produto ao carrinho
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Verificar se produto está ativo
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não está mais disponível para venda.'
            ], 400);
        }

        // Se tiver variation_id, usar dados da variação
        $variation = null;
        if ($request->variation_id) {
            $variation = ProductVariation::where('id', $request->variation_id)
                                         ->where('product_id', $product->id)
                                         ->first();
            
            if (!$variation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variação não encontrada para este produto.'
                ], 404);
            }

            // Validar variação
            if (!$variation->in_stock || $variation->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Variação não disponível em estoque suficiente. Estoque disponível: {$variation->stock_quantity} unidades."
                ], 400);
            }

            $price = $variation->price;
            $stockQuantity = $variation->stock_quantity;
            $inStock = $variation->in_stock;
        } else {
            // Produto sem variação - usar dados do produto
            $price = $product->price;
            $stockQuantity = $product->stock_quantity;
            $inStock = $product->in_stock;
        }
        
        // Verificar se está disponível
        if (!$inStock || $stockQuantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Produto não disponível em estoque suficiente. Estoque disponível: {$stockQuantity} unidades."
            ], 400);
        }

        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Verificar se o item já existe no carrinho (considerando variation_id)
        $existingItem = CartItem::where('product_id', $product->id)
            ->where(function($query) use ($request) {
                if ($request->variation_id) {
                    $query->where('variation_id', $request->variation_id);
                } else {
                    $query->whereNull('variation_id');
                }
            })
            ->where(function ($query) use ($sessionId, $customerId) {
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($existingItem) {
            // Atualizar quantidade
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            // Validar estoque novamente
            $currentStock = $variation ? $variation->stock_quantity : $product->stock_quantity;
            $currentInStock = $variation ? $variation->in_stock : $product->in_stock;
            
            if (!$currentInStock || $newQuantity > $currentStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Quantidade solicitada excede o estoque disponível. Estoque: {$currentStock} unidades."
                ], 400);
            }
            
            // Atualizar preço se mudou
            $currentPrice = $variation ? $variation->price : $product->price;
            if ($currentPrice != $existingItem->price) {
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'price' => $currentPrice
                ]);
            } else {
                $existingItem->update(['quantity' => $newQuantity]);
            }
            $item = $existingItem;
        } else {
            // Criar novo item
            $item = CartItem::create([
                'session_id' => $customerId ? null : $sessionId,
                'customer_id' => $customerId,
                'product_id' => $product->id,
                'variation_id' => $request->variation_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        $cartCount = $this->getCartCount();
        $subtotal = $this->calculateSubtotal($this->getCartItems());
        // Não limpar frete regional/local automaticamente - apenas frete de Correios precisa recalcular
        $shippingSelection = Session::get('shipping_selection');
        if (!empty($shippingSelection) && !empty($shippingSelection['shipping_type']) && $shippingSelection['shipping_type'] === 'correios') {
            // Apenas limpar se for frete dos Correios (que depende de quantidade/peso)
            Session::forget('shipping_selection');
        }

        $displayName = $variation ? $variation->formatted_name : $product->name;

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart_count' => $cartCount,
            'subtotal' => number_format($subtotal, 2, ',', '.'),
            'item' => [
                'id' => $item->id,
                'product_name' => $displayName,
                'quantity' => $item->quantity,
                'price' => number_format($item->price, 2, ',', '.'),
                'total' => number_format($item->total, 2, ',', '.'),
            ]
        ]);
    }

    /**
     * Atualiza quantidade de um item
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        // Verificar se o item pertence ao usuário/sessão
        if (!$this->canModifyItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado no carrinho.'
            ], 404);
        }

        $product = $cartItem->product;
        $variation = $cartItem->variation;
        
        // Verificar se produto ainda está disponível
        if (!$product || !$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não está mais disponível.'
            ], 400);
        }

        // Se tiver variação, validar variação; senão, validar produto
        if ($variation) {
            if (!$variation->in_stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variação não está mais disponível.'
                ], 400);
            }
            
            if ($request->quantity > $variation->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Quantidade solicitada excede o estoque disponível ({$variation->stock_quantity} unidades)."
                ], 400);
            }
        } else {
            if (!$product->in_stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não está mais disponível.'
                ], 400);
            }
            
            if ($request->quantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Quantidade solicitada excede o estoque disponível ({$product->stock_quantity} unidades)."
                ], 400);
            }
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cartCount = $this->getCartCount();
        $subtotal = $this->calculateSubtotal($this->getCartItems());
        // Não limpar frete regional/local - apenas frete de Correios precisa recalcular
        $shippingSelection = Session::get('shipping_selection');
        if (!empty($shippingSelection) && !empty($shippingSelection['shipping_type']) && $shippingSelection['shipping_type'] === 'correios') {
            Session::forget('shipping_selection');
        }

        return response()->json([
            'success' => true,
            'message' => 'Quantidade atualizada!',
            'cart_count' => $cartCount,
            'subtotal' => number_format($subtotal, 2, ',', '.'),
            'item_total' => number_format($cartItem->total, 2, ',', '.'),
            'item_id' => $cartItem->id
        ]);
    }

    /**
     * Remove item do carrinho
     */
    public function remove(CartItem $cartItem)
    {
        // Verificar se o item pertence ao usuário/sessão
        if (!$this->canModifyItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado no carrinho.'
            ], 404);
        }

        $cartItem->delete();

        $cartCount = $this->getCartCount();
        $subtotal = $this->calculateSubtotal($this->getCartItems());
        // Não limpar frete regional/local - apenas frete de Correios precisa recalcular
        $shippingSelection = Session::get('shipping_selection');
        if (!empty($shippingSelection) && !empty($shippingSelection['shipping_type']) && $shippingSelection['shipping_type'] === 'correios') {
            Session::forget('shipping_selection');
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removido do carrinho!',
            'cart_count' => $cartCount,
            'subtotal' => number_format($subtotal, 2, ',', '.'),
        ]);
    }

    /**
     * Limpa o carrinho
     */
    public function clear()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        CartItem::where(function ($query) use ($sessionId, $customerId) {
            if ($customerId) {
                $query->where('customer_id', $customerId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        // Carrinho limpo, limpar seleção de frete
        Session::forget('shipping_selection');

        // Broadcast da atualização do carrinho
        $this->broadcastCartUpdate(0, 0);

        return response()->json([
            'success' => true,
            'message' => 'Carrinho limpo!',
            'cart_count' => 0,
            'subtotal' => '0,00',
        ]);
    }

    /**
     * Obtém contagem de itens no carrinho
     */
    public function count()
    {
        $count = $this->getCartCount();
        return response()->json(['count' => $count]);
    }

    /**
     * Migra carrinho da sessão para o cliente logado
     */
    public function migrateToCustomer()
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['success' => false]);
        }

        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Buscar itens da sessão
        $sessionItems = CartItem::where('session_id', $sessionId)->get();

        foreach ($sessionItems as $sessionItem) {
            // Verificar se já existe item do cliente para o mesmo produto e variação
            $existingItem = CartItem::where('customer_id', $customerId)
                ->where('product_id', $sessionItem->product_id)
                ->where(function($query) use ($sessionItem) {
                    if ($sessionItem->variation_id) {
                        $query->where('variation_id', $sessionItem->variation_id);
                    } else {
                        $query->whereNull('variation_id');
                    }
                })
                ->first();

            if ($existingItem) {
                // Somar quantidades
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $sessionItem->quantity
                ]);
                $sessionItem->delete();
            } else {
                // Migrar item para o cliente
                $sessionItem->update([
                    'session_id' => null,
                    'customer_id' => $customerId
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtém itens do carrinho
     * Garante isolamento total: cada sessão/dispositivo só vê seus próprios itens
     */
    private function getCartItems()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Query estrita: só retornar itens que pertencem EXATAMENTE a esta sessão ou cliente
        $query = CartItem::with(['product', 'variation.attributeValues.attribute', 'variation']);
        
        if ($customerId) {
            // Se logado: só itens do cliente logado E sem session_id
            $query->where('customer_id', $customerId)
                  ->where(function($q) {
                      $q->whereNull('session_id')
                        ->orWhere('session_id', '');
                  });
        } else {
            // Se não logado: só itens da sessão atual E sem customer_id
            $query->where('session_id', $sessionId)
                  ->where(function($q) {
                      $q->whereNull('customer_id')
                        ->orWhere('customer_id', 0);
                  });
        }
        
        return $query->get();
    }

    /**
     * Calcula subtotal do carrinho
     */
    private function calculateSubtotal($cartItems)
    {
        return $cartItems->sum('total');
    }

    /**
     * Obtém contagem de itens no carrinho
     */
    private function getCartCount()
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum('quantity');
    }

    /**
     * Obtém ID da sessão (único por sessão do navegador)
     * Garante que cada navegador/dispositivo tenha seu próprio carrinho isolado
     */
    private function getSessionId()
    {
        $sessionKey = 'cart_session_id';
        
        if (!Session::has($sessionKey)) {
            // Usar o ID da sessão do Laravel como base (já é único por navegador/sessão)
            // Adicionar um hash adicional para garantir unicidade absoluta
            $laravelSessionId = session()->getId();
            $uniqueId = 'cart_' . $laravelSessionId . '_' . md5($laravelSessionId . time() . uniqid('', true));
            
            // Armazenar na sessão do Laravel (que é isolada por navegador)
            Session::put($sessionKey, $uniqueId);
            
            // Garantir que a sessão seja persistida
            Session::save();
        }
        
        return Session::get($sessionKey);
    }

    /**
     * Limpa itens órfãos que não pertencem à sessão atual
     */
    private function cleanOrphanItems($currentSessionId)
    {
        // Não fazer nada se estiver logado
        if (Auth::guard('customer')->check()) {
            return;
        }

        // Buscar itens que não pertencem a nenhuma sessão válida ou são de outras sessões
        // Isso garante que cada navegador/dispositivo tenha seu próprio carrinho isolado
        // Os itens órfãos serão automaticamente ignorados na query principal
    }

    /**
     * Verifica se pode modificar o item
     */
    private function canModifyItem(CartItem $cartItem)
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            return $cartItem->customer_id === $customerId;
        } else {
            return $cartItem->session_id === $sessionId;
        }
    }

    /**
     * Broadcast atualização do carrinho
     */
    private function broadcastCartUpdate($cartCount, $subtotal)
    {
        try {
            event(new CartUpdated([
                'cart_count' => $cartCount,
                'cart_total' => $subtotal,
                'subtotal' => $subtotal
            ]));
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Erro ao fazer broadcast do carrinho: ' . $e->getMessage());
        }
    }

}