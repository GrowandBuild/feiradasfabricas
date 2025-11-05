<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Customer;
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
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        
        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Adiciona produto ao carrinho
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999',
            'variation_id' => 'nullable|exists:product_variations,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Se houver variation_id, usar a variação; caso contrário, usar o produto
        $variation = null;
        $price = $product->price;
        $stockQuantity = $product->stock_quantity;
        $inStock = $product->in_stock;

        if ($request->variation_id) {
            $variation = ProductVariation::findOrFail($request->variation_id);
            if ($variation->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variação não pertence a este produto.'
                ], 400);
            }
            $price = $variation->price;
            $stockQuantity = $variation->stock_quantity;
            $inStock = $variation->in_stock;
        }
        
        // Verificar se está disponível
        if (!$inStock || $stockQuantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não disponível em estoque suficiente.'
            ], 400);
        }

        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Verificar se o item já existe no carrinho (considerando variação)
        $existingItem = CartItem::where('product_id', $product->id)
            ->where('product_variation_id', $request->variation_id)
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
            
            if ($newQuantity > $stockQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantidade solicitada excede o estoque disponível.'
                ], 400);
            }
            
            $existingItem->update(['quantity' => $newQuantity]);
            $item = $existingItem;
        } else {
            // Criar novo item
            $item = CartItem::create([
                'session_id' => $customerId ? null : $sessionId,
                'customer_id' => $customerId,
                'product_id' => $product->id,
                'product_variation_id' => $request->variation_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        $cartCount = $this->getCartCount();
        $subtotal = $this->calculateSubtotal($this->getCartItems());


        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart_count' => $cartCount,
            'subtotal' => number_format($subtotal, 2, ',', '.'),
            'item' => [
                'id' => $item->id,
                'product_name' => $product->name,
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
        
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Quantidade solicitada excede o estoque disponível.'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cartCount = $this->getCartCount();
        $subtotal = $this->calculateSubtotal($this->getCartItems());


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
            // Verificar se já existe item do cliente para o mesmo produto
            $existingItem = CartItem::where('customer_id', $customerId)
                ->where('product_id', $sessionItem->product_id)
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
     */
    private function getCartItems()
    {
        $sessionId = $this->getSessionId();
        $customerId = Auth::guard('customer')->id();

        // Se não há customer_id, tentar migrar itens órfãos para a sessão atual
        if (!$customerId) {
            $this->migrateOrphanItems($sessionId);
        }

        return CartItem::with('product')
            ->where(function ($query) use ($sessionId, $customerId) {
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();
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
     * Obtém ID da sessão
     */
    private function getSessionId()
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', uniqid());
        }
        return Session::get('cart_session_id');
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
     * Migra itens órfãos para a sessão atual
     */
    private function migrateOrphanItems($sessionId)
    {
        // Buscar itens órfãos (sem customer_id e com session_id diferente)
        $orphanItems = CartItem::whereNull('customer_id')
            ->where('session_id', '!=', $sessionId)
            ->get();

        if ($orphanItems->count() > 0) {
            // Migrar todos os itens órfãos para a sessão atual
            foreach ($orphanItems as $item) {
                // Verificar se já existe item na sessão atual para o mesmo produto
                $existingItem = CartItem::where('session_id', $sessionId)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($existingItem) {
                    // Somar quantidades
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item->quantity
                    ]);
                    $item->delete();
                } else {
                    // Migrar item para a sessão atual
                    $item->update(['session_id' => $sessionId]);
                }
            }

            \Log::info('Itens órfãos migrados', [
                'session_id' => $sessionId,
                'items_migrated' => $orphanItems->count()
            ]);
        }
    }

}