<?php

namespace Nerdcel\BladeExtensions;

use Illuminate\Support\Facades\Blade as BladeFacade;

class Register
{
    public string $key;

    public function register(): void
    {
        $kirby = kirby();
        $loop = $kirby->option('nerdcel.kirby3-blade-extensions.' . $this->key, []);

        switch (get_class($this)) {
            case Directives::class:
                $loop = [...Directives::getInstance()->list(), ...$loop];

                foreach ($loop as $statement => $callback) {
                    BladeFacade::directive($statement, $callback);
                }
                break;
            case Ifs::class:
                $loop = [...Ifs::getInstance()->list(), ...$loop];

                foreach ($loop as $statement => $callback) {
                    BladeFacade::if($statement, $callback);
                }
                break;
            default:
                return;
        }
    }
}
