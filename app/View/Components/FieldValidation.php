<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class FieldValidation extends Component
{
    public string $errorname;
    /**
     * Create a new component instance.
     */
    public function __construct(string $errorname)
    {
        $this->errorname = $errorname;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.field-validation');
    }
}
