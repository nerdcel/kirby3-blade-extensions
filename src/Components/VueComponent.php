<?php

namespace Nerdcel\BladeExtensions\Components;

use Illuminate\View\Component;

class VueComponent extends Component
{
    public string $name;
    public ?string $className;

    public function __construct($name, $class = null)
    {
        $this->name = $name;
        $this->className = $class;
    }

    public function render()
    {
        return <<<'blade'
            <div data-js-component="{{ $name }}" @if($className)class="{{ $className }}"@endif>
                {{ $slot }}
            </div>
        blade;
    }
}
