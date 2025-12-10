<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Lista de desejos do cliente ou admin
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$customer && !$admin) {
            return redirect()->route('login')->with('error', 'Faça login para ver sua lista de desejos.');
        }

        if ($customer) {
            $favorites = Favorite::where('customer_id', $customer->id)
                ->with('product')
                ->latest()
                ->paginate(12);
        } else {
            $favorites = Favorite::where('admin_id', $admin->id)
                ->with('product')
                ->latest()
                ->paginate(12);
        }

        return view('wishlist.index', compact('favorites'));
    }

    /**
     * Adicionar produto à lista de desejos
     */
    public function store(Request $request, $product)
    {
        // Buscar produto pelo slug (se for string) ou ID (se for número)
        if (is_numeric($product)) {
            $product = Product::findOrFail($product);
        } else {
            $product = Product::where('slug', $product)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user();
        $admin = Auth::guard('admin')->user();

        if (!$customer && !$admin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faça login para adicionar produtos à lista de desejos.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Faça login para adicionar produtos à lista de desejos.');
        }

        // Verificar se já está nos favoritos
        if ($customer) {
            $exists = Favorite::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->exists();
            $countField = 'customer_id';
            $userId = $customer->id;
            $count = $customer->favorites()->count();
        } else {
            $exists = Favorite::where('admin_id', $admin->id)
                ->where('product_id', $product->id)
                ->exists();
            $countField = 'admin_id';
            $userId = $admin->id;
            $count = $admin->favorites()->count();
        }

        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este produto já está na sua lista de desejos.'
                ], 400);
            }
            return back()->with('info', 'Este produto já está na sua lista de desejos.');
        }

        Favorite::create([
            $countField => $userId,
            'product_id' => $product->id,
        ]);

        $count++;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produto adicionado à lista de desejos!',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Produto adicionado à lista de desejos!');
    }

    /**
     * Remover produto da lista de desejos
     */
    public function destroy(Request $request, $product)
    {
        // Buscar produto pelo slug (se for string) ou ID (se for número)
        if (is_numeric($product)) {
            $product = Product::findOrFail($product);
        } else {
            $product = Product::where('slug', $product)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user();
        $admin = Auth::guard('admin')->user();

        if (!$customer && !$admin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autorizado.'
                ], 401);
            }
            return redirect()->route('login');
        }

        if ($customer) {
            Favorite::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->delete();
            $count = $customer->favorites()->count();
        } else {
            Favorite::where('admin_id', $admin->id)
                ->where('product_id', $product->id)
                ->delete();
            $count = $admin->favorites()->count();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produto removido da lista de desejos.',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Produto removido da lista de desejos.');
    }

    /**
     * Toggle (adicionar/remover) produto da lista de desejos
     */
    public function toggle(Request $request, $product)
    {
        // Buscar produto pelo slug (se for string) ou ID (se for número)
        if (is_numeric($product)) {
            $product = Product::findOrFail($product);
        } else {
            $product = Product::where('slug', $product)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user();
        $admin = Auth::guard('admin')->user();

        if (!$customer && !$admin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faça login para gerenciar sua lista de desejos.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('info', 'Faça login para gerenciar sua lista de desejos.');
        }

        // Verificar se já existe favorito
        if ($customer) {
            $favorite = Favorite::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->first();
            $countField = 'customer_id';
            $userId = $customer->id;
            $count = $customer->favorites()->count();
        } else {
            $favorite = Favorite::where('admin_id', $admin->id)
                ->where('product_id', $product->id)
                ->first();
            $countField = 'admin_id';
            $userId = $admin->id;
            $count = $admin->favorites()->count();
        }

        if ($favorite) {
            $favorite->delete();
            $added = false;
            $message = 'Produto removido da lista de desejos.';
            $count--;
        } else {
            Favorite::create([
                $countField => $userId,
                'product_id' => $product->id,
            ]);
            $added = true;
            $message = 'Produto adicionado à lista de desejos!';
            $count++;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return back()->with('success', $message);
    }
}
