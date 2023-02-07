<?php

namespace Nerdcel\BladeExtensions;

use KirbyHelpers\Vite;
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

                if (class_exists(Vite::class)) {
                    return Vite::instance()->isDev();
                }

                return false;
            },
        ];
    }
}
