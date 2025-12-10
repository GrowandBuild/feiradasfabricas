<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PwaInstallPrompt extends Component
{
    public function render(): View
    {
        return view('components.pwa-install-prompt');
    }
}
