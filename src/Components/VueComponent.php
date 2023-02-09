<?php

namespace Nerdcel\BladeExtensions\Components;

use Illuminate\View\Component;

class VueComponent extends Component
{
    public string $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function render()
    {
        return <<<'blade'
            <div data-js-component="{{ $name }}">
                {{ $slot }}
            </div>
        blade;
    }
}
