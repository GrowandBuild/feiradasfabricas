<?php

namespace App\View\Components;

use App\Models\PromotionalBadge as PromotionalBadgeModel;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;
use Illuminate\View\View;

class PromotionalBadge extends Component
{
    public function render(): View
    {
        try {
            $badge = PromotionalBadgeModel::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Exception $e) {
            Log::warning('PromotionalBadge component DB error: ' . $e->getMessage());
            $badge = null;
        }

        return view('components.promotional-badge', [
            'badge' => $badge
        ]);
    }
}
