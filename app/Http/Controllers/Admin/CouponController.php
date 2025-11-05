<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('starts_at')
                            ->orWhere('starts_at', '<=', now());
                      })
                      ->where(function ($q) {
                          $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', now());
                      });
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->withCount('usages')->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'customer_type' => 'required|in:all,b2c,b2b',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->type === 'percentage' && $request->value > 100) {
            return redirect()->back()
                           ->with('error', 'O desconto percentual não pode ser maior que 100%.')
                           ->withInput();
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Cupom criado com sucesso!');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.customer', 'usages.order']);
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'customer_type' => 'required|in:all,b2c,b2b',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->type === 'percentage' && $request->value > 100) {
            return redirect()->back()
                           ->with('error', 'O desconto percentual não pode ser maior que 100%.')
                           ->withInput();
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Cupom atualizado com sucesso!');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->usages()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Não é possível excluir um cupom que já foi utilizado.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Cupom excluído com sucesso!');
    }

    public function toggleActive(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        
        $status = $coupon->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
                        ->with('success', "Cupom {$status} com sucesso!");
    }
}
