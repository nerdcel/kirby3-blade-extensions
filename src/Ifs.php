<?php

namespace Nerdcel\BladeExtensions;

use Nerdcel\BladeExtensions\Contracts\Registrable;
use Nerdcel\BladeExtensions\Traits\Singleton;

class Ifs extends Register implements Registrable
{
    use Singleton;

    public function __construct()
    {
        $this->key = 'ifs';
    }

    public function list(): array
    {
        return [
            'viteDevMode' => function (): bool {

                if (class_exists(\JohannSchopplich\Helpers\Vite::class)) {
                    return \JohannSchopplich\Helpers\Vite::instance()->isDev();
                }

                return false;
            },
        ];
    }
}
