<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Log para debug
        \Log::info('Tentativa de login', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Tentar login como cliente primeiro
        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            // Migrar carrinho da sessão antes de regenerar
            $this->migrateCartToCustomer($request);
            
            $request->session()->regenerate();
            \Log::info('Login como cliente bem-sucedido', ['email' => $request->email]);
            return redirect()->intended(route('home'));
        }

        \Log::info('Login como cliente falhou, tentando como admin', ['email' => $request->email]);

        // Se não conseguir como cliente, tentar como admin
        $admin = Admin::where('email', $request->email)
                     ->where('is_active', true)
                     ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            \Log::info('Admin encontrado e senha correta', [
                'admin_id' => $admin->id,
                'admin_name' => $admin->name
            ]);

            // Fazer login como admin no guard admin
            Auth::guard('admin')->login($admin, $request->boolean('remember'));
            $request->session()->regenerate();
            
            \Log::info('Login como admin bem-sucedido', ['admin_id' => $admin->id]);
            
            // Redirecionar para o painel admin se for admin
            return redirect()->intended(route('admin.dashboard'));
        }

        \Log::warning('Login falhou para todas as tentativas', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Exibe o formulário de registro B2C
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Exibe o formulário de registro B2B
     */
    public function showB2BRegisterForm()
    {
        return view('auth.register-b2b');
    }

    /**
     * Processa o registro B2C
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
        ]);

        $customer = Customer::create([
            'type' => 'b2c',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('home')->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Processa o registro B2B
     */
    public function registerB2B(Request $request)
    {
        // Validar CNPJ
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        if (strlen($cnpj) !== 14 || !$this->validateCNPJ($cnpj)) {
            return back()->withErrors(['cnpj' => 'CNPJ inválido. Por favor, verifique o número.'])->withInput();
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'company_name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:customers',
            'ie' => 'nullable|string|max:20',
            'contact_person' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'zip_code' => 'required|string|max:10',
        ]);

        $customer = Customer::create([
            'type' => 'b2b',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'cnpj' => $request->cnpj,
            'ie' => $request->ie,
            'contact_person' => $request->contact_person,
            'department' => $request->department,
            'address' => $request->address,
            'number' => $request->number,
            'complement' => $request->complement,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'b2b_status' => 'pending',
            'is_active' => true,
        ]);

        // Enviar email de notificação para admin sobre novo cadastro B2B
        try {
            $emailService = app(\App\Services\EmailService::class);
            $emailService->enviarNotificacaoCadastroB2B($customer);
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar notificação de cadastro B2B: ' . $e->getMessage());
            // Não interromper o fluxo se o email falhar
        }
        
        return redirect()->route('home')->with('success', 'Sua solicitação de conta B2B foi enviada com sucesso! Nossa equipe analisará seu cadastro e você receberá um e-mail de confirmação em até 24 horas.');
    }

    /**
     * Valida CNPJ
     */
    private function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^\d]/', '', $cnpj);
        
        if (strlen($cnpj) !== 14) {
            return false;
        }
        
        // Elimina CNPJs conhecidos como inválidos
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Validação dos dígitos verificadores
        $length = strlen($cnpj) - 2;
        $numbers = substr($cnpj, 0, $length);
        $digits = substr($cnpj, $length);
        $sum = 0;
        $pos = $length - 7;
        
        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        
        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $digits[0]) {
            return false;
        }
        
        $length = $length + 1;
        $numbers = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;
        
        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        
        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $digits[1]) {
            return false;
        }
        
        return true;
    }

    /**
     * Migra carrinho da sessão para o cliente logado
     */
    private function migrateCartToCustomer($request)
    {
        try {
            $sessionId = $request->session()->get('cart_session_id');
            if (!$sessionId) {
                return;
            }

            $customerId = Auth::guard('customer')->id();
            if (!$customerId) {
                return;
            }

            // Buscar itens da sessão
            $sessionItems = \App\Models\CartItem::where('session_id', $sessionId)->get();

            foreach ($sessionItems as $sessionItem) {
                // Verificar se já existe item do cliente para o mesmo produto
                $existingItem = \App\Models\CartItem::where('customer_id', $customerId)
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

            \Log::info('Carrinho migrado para cliente', [
                'customer_id' => $customerId,
                'session_id' => $sessionId,
                'items_migrated' => $sessionItems->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao migrar carrinho: ' . $e->getMessage());
        }
    }
}
