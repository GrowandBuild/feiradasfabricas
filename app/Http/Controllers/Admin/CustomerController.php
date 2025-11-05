<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('b2b_status')) {
            $query->where('b2b_status', $request->b2b_status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $customers = $query->withCount('orders')->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.orderItems.product']);
        $orders = $customer->orders()->with('orderItems.product')->latest()->limit(10)->get();
        
        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'ie' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'b2b_status' => 'nullable|in:pending,approved,rejected',
            'b2b_notes' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $customer->update($data);

        return redirect()->route('admin.customers.index')
                        ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function updateB2BStatus(Request $request, Customer $customer)
    {
        $request->validate([
            'b2b_status' => 'required|in:pending,approved,rejected',
            'b2b_notes' => 'nullable|string',
        ]);

        $customer->update([
            'b2b_status' => $request->b2b_status,
            'b2b_notes' => $request->b2b_notes,
        ]);

        $statusLabels = [
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
        ];

        return redirect()->back()
                        ->with('success', 'Status B2B atualizado para: ' . $statusLabels[$request->b2b_status]);
    }

    public function resetPassword(Request $request, Customer $customer)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()
                        ->with('success', 'Senha redefinida com sucesso!');
    }

    public function toggleActive(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);

        $status = $customer->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
                        ->with('success', "Cliente {$status} com sucesso!");
    }

    public function getOrders(Customer $customer)
    {
        $orders = $customer->orders()->with('orderItems.product')->latest()->paginate(10);
        return view('admin.customers.orders', compact('customer', 'orders'));
    }
}
