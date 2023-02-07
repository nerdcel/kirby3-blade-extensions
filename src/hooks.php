<?php

use Nerdcel\BladeExtensions\Directives;
use Nerdcel\BladeExtensions\Ifs;

return [
    // Add directives to.blade
    'system.loadPlugins:after' => function () {
        Directives::getInstance()->register();
        Ifs::getInstance()->register();
    }
];
