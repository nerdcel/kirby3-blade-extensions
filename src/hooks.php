<?php

use Nerdcel\BladeExtensions\Components;
use Nerdcel\BladeExtensions\Directives;
use Nerdcel\BladeExtensions\Ifs;

return [
    // Add directives to.blade
    'system.loadPlugins:after' => function () {
        Directives::getInstance()->register();
        Ifs::getInstance()->register();
        Components::getInstance()->register();
    }
];
