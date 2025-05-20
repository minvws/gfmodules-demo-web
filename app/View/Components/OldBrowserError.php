<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class OldBrowserError extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render()
    {
        return view('components.old-browser-error');
    }
}
