<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HoverEffectsController extends Controller
{
    public function index()
    {
        // Carregar configurações atuais de hover
        $hoverEffects = [
            'enabled' => Setting::get('hover_effects_enabled', true),
            'transform_scale' => Setting::get('hover_transform_scale', 1.05),
            'transform_translate_y' => Setting::get('hover_transform_translate_y', -8),
            'shadow_intensity' => Setting::get('hover_shadow_intensity', 24),
            'transition_duration' => Setting::get('hover_transition_duration', 0.3),
            'transition_easing' => Setting::get('hover_transition_easing', 'cubic-bezier(0.4, 0, 0.2, 1)'),
            'border_color_intensity' => Setting::get('hover_border_color_intensity', 0.8),
        ];

        return view('admin.hover-effects.index', compact('hoverEffects'));
    }

    public function update(Request $request)
    {
        // Converter checkbox para boolean
        $request->merge([
            'hover_effects_enabled' => $request->has('hover_effects_enabled') && $request->hover_effects_enabled === '1'
        ]);

        $validated = $request->validate([
            'hover_effects_enabled' => 'boolean',
            'hover_transform_scale' => 'required|numeric|min:0.5|max:2',
            'hover_transform_translate_y' => 'required|numeric|min:-50|max:50',
            'hover_shadow_intensity' => 'required|numeric|min:0|max:100',
            'hover_transition_duration' => 'required|numeric|min:0.1|max:2',
            'hover_transition_easing' => 'required|string|in:linear,ease,ease-in,ease-out,ease-in-out,cubic-bezier(0.4, 0, 0.2, 1),cubic-bezier(0.25, 0.46, 0.45, 0.94)',
            'hover_border_color_intensity' => 'required|numeric|min:0|max:1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurações de hover atualizadas com sucesso!'
        ]);
    }
}
