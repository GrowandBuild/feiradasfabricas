<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepartmentBadge;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepartmentBadgeController extends Controller
{
    public function index(Request $request)
    {
        $query = DepartmentBadge::with('department')->orderBy('department_id')->orderBy('sort_order');
        
        // Filtro por departamento
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $badges = $query->paginate(20);
        $departments = Department::active()->ordered()->get();
        
        return view('admin.department-badges.index', compact('badges', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->ordered()->get();
        return view('admin.department-badges.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'department_id.required' => 'O departamento é obrigatório.',
            'department_id.exists' => 'O departamento selecionado não existe.',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'image.required' => 'A imagem é obrigatória.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem não pode ter mais de 5MB.',
            'link.max' => 'O link não pode ter mais de 255 caracteres.',
            'sort_order.integer' => 'A ordem de exibição deve ser um número inteiro.',
            'sort_order.min' => 'A ordem de exibição deve ser no mínimo 0.',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            // Upload da imagem
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('department-badges', 'public');
            }

            DepartmentBadge::create($data);

            return redirect()->route('admin.department-badges.index')
                            ->with('success', 'Selo criado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar selo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Erro ao salvar selo: ' . $e->getMessage()]);
        }
    }

    public function edit(DepartmentBadge $departmentBadge)
    {
        $departments = Department::active()->ordered()->get();
        return view('admin.department-badges.edit', compact('departmentBadge', 'departments'));
    }

    public function update(Request $request, DepartmentBadge $departmentBadge)
    {
        $request->merge([
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'department_id.required' => 'O departamento é obrigatório.',
            'department_id.exists' => 'O departamento selecionado não existe.',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem não pode ter mais de 5MB.',
            'link.max' => 'O link não pode ter mais de 255 caracteres.',
            'sort_order.integer' => 'A ordem de exibição deve ser um número inteiro.',
            'sort_order.min' => 'A ordem de exibição deve ser no mínimo 0.',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            // Upload da nova imagem (se fornecida)
            if ($request->hasFile('image')) {
                // Deletar imagem antiga
                if ($departmentBadge->image && Storage::disk('public')->exists($departmentBadge->image)) {
                    Storage::disk('public')->delete($departmentBadge->image);
                }
                $data['image'] = $request->file('image')->store('department-badges', 'public');
            } else {
                // Manter imagem existente
                unset($data['image']);
            }

            $departmentBadge->update($data);

            return redirect()->route('admin.department-badges.index')
                            ->with('success', 'Selo atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar selo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Erro ao atualizar selo: ' . $e->getMessage()]);
        }
    }

    public function destroy(DepartmentBadge $departmentBadge)
    {
        try {
            // Deletar imagem
            if ($departmentBadge->image && Storage::disk('public')->exists($departmentBadge->image)) {
                Storage::disk('public')->delete($departmentBadge->image);
            }

            $departmentBadge->delete();

            return redirect()->route('admin.department-badges.index')
                            ->with('success', 'Selo excluído com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir selo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'badge_id' => $departmentBadge->id
            ]);

            return redirect()->back()
                            ->withErrors(['error' => 'Erro ao excluir selo: ' . $e->getMessage()]);
        }
    }

    public function toggleActive(DepartmentBadge $departmentBadge)
    {
        try {
            $departmentBadge->update([
                'is_active' => !$departmentBadge->is_active
            ]);

            $status = $departmentBadge->is_active ? 'ativado' : 'desativado';
            return redirect()->back()
                            ->with('success', "Selo {$status} com sucesso!");
        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status do selo: ' . $e->getMessage());
            return redirect()->back()
                            ->withErrors(['error' => 'Erro ao alterar status do selo.']);
        }
    }

    /**
     * Atualização rápida do título (JSON) para atalhos inline
     */
    public function updateTitle(Request $request, DepartmentBadge $departmentBadge)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            $departmentBadge->update(['title' => $request->title]);
            return response()->json([
                'success' => true,
                'badge' => [
                    'id' => $departmentBadge->id,
                    'title' => $departmentBadge->title,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar título do selo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar título do selo.'
            ], 500);
        }
    }

    /**
     * Atualização rápida da imagem (JSON) para atalhos inline
     */
    public function updateImage(Request $request, DepartmentBadge $departmentBadge)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,avif|max:10240',
        ], [
            'image.required' => 'Escolha uma imagem válida.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'Formatos permitidos: jpeg, png, jpg, gif, webp, avif.',
            'image.max' => 'Tamanho máximo: 10MB.',
        ]);

        try {
            // Deleta a imagem antiga se existir
            if ($departmentBadge->image && Storage::disk('public')->exists($departmentBadge->image)) {
                Storage::disk('public')->delete($departmentBadge->image);
            }

            $path = $request->file('image')->store('department-badges', 'public');
            $departmentBadge->update(['image' => $path]);

            return response()->json([
                'success' => true,
                'badge' => [
                    'id' => $departmentBadge->id,
                    'image_url' => $departmentBadge->image_url,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar imagem do selo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar imagem do selo.'
            ], 500);
        }
    }

    /**
     * Remoção rápida da imagem (JSON) para atalhos inline
     */
    public function removeImage(Request $request, DepartmentBadge $departmentBadge)
    {
        try {
            if ($departmentBadge->image && Storage::disk('public')->exists($departmentBadge->image)) {
                Storage::disk('public')->delete($departmentBadge->image);
            }
            $departmentBadge->update(['image' => null]);

            return response()->json([
                'success' => true,
                'badge' => [
                    'id' => $departmentBadge->id,
                    'image_url' => asset('images/no-image.svg'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao remover imagem do selo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem do selo.'
            ], 500);
        }
    }
}
