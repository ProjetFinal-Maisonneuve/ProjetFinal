<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public string $logoPath;
    /**
     * Create a new component instance.
     */
    public function __construct(string $logoPath)
    {
        $this->logoPath = $logoPath;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header');
    }
}
