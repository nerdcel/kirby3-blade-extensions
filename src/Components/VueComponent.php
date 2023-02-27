<?php

namespace Nerdcel\BladeExtensions\Components;

use Illuminate\View\Component;

class VueComponent extends Component
{
    public string $name;
    public ?string $className;

    public ?bool $cloak;

    public function __construct($name, $class = null, $cloak = false)
    {
        $this->name = $name;
        $this->className = $class;
        $this->cloak = $cloak;
    }

    public function render()
    {
        return <<<'blade'
            <div data-js-component="{{ $name }}" @if($className)class="{{ $className }}"@endif @if($cloak) data-v-cloak @endif>
                {{ $slot }}
            </div>
        blade;
    }
}
