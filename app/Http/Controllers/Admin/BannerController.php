<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::with('department')->orderBy('position')->orderBy('sort_order');
        
        // Filtro por departamento
        if ($request->filled('department_id')) {
            if ($request->department_id === 'global') {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $request->department_id);
            }
        }
        
        // Filtro por posição
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $banners = $query->paginate(20);
        $departments = Department::active()->ordered()->get();
        
        return view('admin.banners.index', compact('banners', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->ordered()->get();
        return view('admin.banners.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'link' => 'nullable|string|max:255',
            'position' => 'required|in:hero,category,product,footer',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'target_audience' => 'required|in:all,b2c,b2b',
            'department_id' => 'nullable|exists:departments,id',
            // Campos de estilo
            'text_color' => 'nullable|string|max:7',
            'text_size' => 'nullable|string|max:10',
            'text_align' => 'nullable|in:left,center,right',
            'text_font_weight' => 'nullable|in:300,400,600,700,800',
            'text_padding_top' => 'nullable|integer|min:0|max:100',
            'text_padding_bottom' => 'nullable|integer|min:0|max:100',
            'text_padding_left' => 'nullable|integer|min:0|max:100',
            'text_padding_right' => 'nullable|integer|min:0|max:100',
            'text_margin_top' => 'nullable|integer|min:0|max:100',
            'text_margin_bottom' => 'nullable|integer|min:0|max:100',
            'text_margin_left' => 'nullable|integer|min:0|max:100',
            'text_margin_right' => 'nullable|integer|min:0|max:100',
            'text_shadow_color' => 'nullable|string|max:7',
            'text_shadow_blur' => 'nullable|integer|min:0|max:20',
            'description_color' => 'nullable|string|max:7',
            'description_size' => 'nullable|string|max:10',
            'description_align' => 'nullable|in:left,center,right',
            'description_margin_top' => 'nullable|integer|min:0|max:100',
            'banner_background_color' => 'nullable|string|max:7',
            'banner_height' => 'nullable|string|max:10',
            'banner_padding_top' => 'nullable|integer|min:0|max:100',
            'banner_padding_bottom' => 'nullable|integer|min:0|max:100',
            'show_title' => 'nullable|boolean',
            'show_description' => 'nullable|boolean',
            'show_overlay' => 'nullable|boolean',
            'overlay_color' => 'nullable|string|max:20',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['show_title'] = $request->has('show_title');
        $data['show_description'] = $request->has('show_description');
        $data['show_overlay'] = $request->has('show_overlay');

        // Garantir que campos de cor vazios sejam null (para permitir reset)
        $colorFields = ['text_color', 'description_color', 'text_shadow_color', 'banner_background_color', 'overlay_color'];
        foreach ($colorFields as $field) {
            if (isset($data[$field]) && empty(trim($data[$field]))) {
                $data[$field] = null;
            }
        }

        try {
            // Upload da imagem principal
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('banners', 'public');
            }

            // Upload da imagem mobile
            if ($request->hasFile('mobile_image')) {
                $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
            }

            // Remover campos que não devem ser salvos no banco
            unset($data['_token'], $data['_method']);

            $banner = Banner::create($data);

            return redirect()->route('admin.banners.index')
                            ->with('success', 'Banner criado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar banner: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Erro ao salvar banner: ' . $e->getMessage()]);
        }
    }

    public function edit(Banner $banner)
    {
        $departments = Department::active()->ordered()->get();
        return view('admin.banners.edit', compact('banner', 'departments'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'position' => 'required|in:hero,category,product,footer',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'target_audience' => 'required|in:all,b2c,b2b',
            'department_id' => 'nullable|exists:departments,id',
            // Campos de estilo
            'text_color' => 'nullable|string|max:7',
            'text_size' => 'nullable|string|max:10',
            'text_align' => 'nullable|in:left,center,right',
            'text_font_weight' => 'nullable|in:300,400,600,700,800',
            'text_padding_top' => 'nullable|integer|min:0|max:100',
            'text_padding_bottom' => 'nullable|integer|min:0|max:100',
            'text_padding_left' => 'nullable|integer|min:0|max:100',
            'text_padding_right' => 'nullable|integer|min:0|max:100',
            'text_margin_top' => 'nullable|integer|min:0|max:100',
            'text_margin_bottom' => 'nullable|integer|min:0|max:100',
            'text_margin_left' => 'nullable|integer|min:0|max:100',
            'text_margin_right' => 'nullable|integer|min:0|max:100',
            'text_shadow_color' => 'nullable|string|max:7',
            'text_shadow_blur' => 'nullable|integer|min:0|max:20',
            'description_color' => 'nullable|string|max:7',
            'description_size' => 'nullable|string|max:10',
            'description_align' => 'nullable|in:left,center,right',
            'description_margin_top' => 'nullable|integer|min:0|max:100',
            'banner_background_color' => 'nullable|string|max:7',
            'banner_height' => 'nullable|string|max:10',
            'banner_padding_top' => 'nullable|integer|min:0|max:100',
            'banner_padding_bottom' => 'nullable|integer|min:0|max:100',
            'show_title' => 'nullable|boolean',
            'show_description' => 'nullable|boolean',
            'show_overlay' => 'nullable|boolean',
            'overlay_color' => 'nullable|string|max:20',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['show_title'] = $request->has('show_title');
        $data['show_description'] = $request->has('show_description');
        $data['show_overlay'] = $request->has('show_overlay');

        // Garantir que campos de cor vazios sejam null (para permitir reset)
        $colorFields = ['text_color', 'description_color', 'text_shadow_color', 'banner_background_color', 'overlay_color'];
        foreach ($colorFields as $field) {
            if (isset($data[$field]) && empty(trim($data[$field]))) {
                $data[$field] = null;
            }
        }

        // Processar imagem desktop
        if ($request->hasFile('image')) {
            // Upload tem prioridade - remover imagem anterior
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->has('remove_image') && $request->remove_image == '1') {
            // Remover imagem desktop se solicitado (apenas se não houver upload)
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = null;
            // Garantir que o campo seja realmente atualizado
            unset($data['remove_image']);
        } else {
            // Se não há upload nem remoção, manter o valor atual (não incluir no update)
            unset($data['image']);
        }
        
        // Remover campos que não devem ser atualizados diretamente
        unset($data['remove_image'], $data['remove_mobile_image']);

        // Processar imagem mobile
        if ($request->hasFile('mobile_image')) {
            // Upload tem prioridade - remover imagem anterior
            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
        } elseif ($request->has('remove_mobile_image') && $request->remove_mobile_image == '1') {
            // Remover imagem mobile se solicitado (apenas se não houver upload)
            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            $data['mobile_image'] = null;
            // Garantir que o campo seja realmente atualizado
            unset($data['remove_mobile_image']);
        } else {
            // Se não há upload nem remoção, manter o valor atual (não incluir no update)
            unset($data['mobile_image']);
        }

        try {
            // Remover campos que não devem ser atualizados diretamente
            unset($data['_token'], $data['_method']);

            // Atualizar o banner
            $banner->update($data);
            
            // Recarregar o banner do banco de dados para garantir que as alterações estejam refletidas
            $banner = $banner->fresh();

            return redirect()->route('admin.banners.edit', $banner)
                            ->with('success', 'Banner atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar banner: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Erro ao atualizar banner: ' . $e->getMessage()]);
        }
    }

    public function destroy(Banner $banner)
    {
        // Remover imagens
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        if ($banner->mobile_image) {
            Storage::disk('public')->delete($banner->mobile_image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner excluído com sucesso!');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        
        $status = $banner->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
                        ->with('success', "Banner {$status} com sucesso!");
    }
}
