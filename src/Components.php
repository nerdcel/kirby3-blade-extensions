<?php

namespace Nerdcel\BladeExtensions;

use Illuminate\Support\Facades\Blade;
use Nerdcel\BladeExtensions\Components\VueComponent;
use Nerdcel\BladeExtensions\Contracts\Registrable;
use Nerdcel\BladeExtensions\Traits\Singleton;

class Components extends Register implements Registrable
{
    use Singleton;

    public function __construct()
    {
        $this->key = 'components';
    }

    public function list(): array
    {
        return [
            'vueComponent' => function () {
                Blade::component('vue-component', VueComponent::class);
            }
        ];
    }
}
