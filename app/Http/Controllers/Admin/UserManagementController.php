<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth('admin')->user()->hasPermission('users.manage')) {
                abort(403, 'Você não tem permissão para gerenciar usuários.');
            }
            return $next($request);
        });
    }

    /**
     * Listar usuários admin
     */
    public function index(Request $request)
    {
        $query = Admin::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        $roles = $this->getAvailableRoles();
        $permissions = $this->getAvailablePermissions();
        
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Criar novo usuário
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = $request->has('is_active');

        // Se for super_admin, dar todas as permissões
        if ($data['role'] === 'super_admin') {
            $data['permissions'] = ['*'];
        } else {
            $data['permissions'] = $request->permissions ?? [];
        }

        Admin::create($data);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Mostrar usuário
     */
    public function show(Admin $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(Admin $user)
    {
        $roles = $this->getAvailableRoles();
        $permissions = $this->getAvailablePermissions();
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, Admin $user)
    {
        // Não permitir editar o próprio usuário se não for super_admin
        if ($user->id === auth('admin')->id() && !auth('admin')->user()->isSuperAdmin()) {
            return redirect()->back()
                            ->with('error', 'Você não pode editar seu próprio usuário.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['password', 'password_confirmation']);
        $data['is_active'] = $request->has('is_active');

        // Atualizar senha se fornecida
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Se for super_admin, dar todas as permissões
        if ($data['role'] === 'super_admin') {
            $data['permissions'] = ['*'];
        } else {
            $data['permissions'] = $request->permissions ?? [];
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Deletar usuário
     */
    public function destroy(Admin $user)
    {
        // Não permitir deletar a si mesmo
        if ($user->id === auth('admin')->id()) {
            return redirect()->back()
                            ->with('error', 'Você não pode deletar seu próprio usuário.');
        }

        // Não permitir deletar o último super_admin
        if ($user->isSuperAdmin() && Admin::where('role', 'super_admin')->count() <= 1) {
            return redirect()->back()
                            ->with('error', 'Não é possível deletar o último super administrador.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário deletado com sucesso!');
    }

    /**
     * Alternar status ativo/inativo
     */
    public function toggleStatus(Admin $user)
    {
        // Não permitir desativar a si mesmo
        if ($user->id === auth('admin')->id()) {
            return redirect()->back()
                            ->with('error', 'Você não pode desativar seu próprio usuário.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
                        ->with('success', "Usuário {$status} com sucesso!");
    }

    /**
     * Resetar senha
     */
    public function resetPassword(Admin $user)
    {
        $newPassword = 'password123'; // Senha temporária
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->back()
                        ->with('success', "Senha resetada para: {$newPassword}");
    }

    /**
     * Obter roles disponíveis
     */
    private function getAvailableRoles()
    {
        return [
            'admin' => 'Administrador',
            'super_admin' => 'Super Administrador'
        ];
    }

    /**
     * Obter permissões disponíveis
     */
    private function getAvailablePermissions()
    {
        return [
            'dashboard.view' => 'Visualizar Dashboard',
            'products.view' => 'Visualizar Produtos',
            'products.create' => 'Criar Produtos',
            'products.edit' => 'Editar Produtos',
            'products.delete' => 'Deletar Produtos',
            'categories.view' => 'Visualizar Categorias',
            'categories.create' => 'Criar Categorias',
            'categories.edit' => 'Editar Categorias',
            'categories.delete' => 'Deletar Categorias',
            'orders.view' => 'Visualizar Pedidos',
            'orders.edit' => 'Editar Pedidos',
            'customers.view' => 'Visualizar Clientes',
            'customers.edit' => 'Editar Clientes',
            'users.view' => 'Visualizar Usuários',
            'users.create' => 'Criar Usuários',
            'users.edit' => 'Editar Usuários',
            'users.delete' => 'Deletar Usuários',
            'users.manage' => 'Gerenciar Usuários',
            'settings.view' => 'Visualizar Configurações',
            'settings.edit' => 'Editar Configurações',
            'reports.view' => 'Visualizar Relatórios',
            'coupons.view' => 'Visualizar Cupons',
            'coupons.create' => 'Criar Cupons',
            'coupons.edit' => 'Editar Cupons',
            'banners.view' => 'Visualizar Banners',
            'banners.create' => 'Criar Banners',
            'banners.edit' => 'Editar Banners',
        ];
    }
}
