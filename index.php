<?php

@include_once __DIR__ . '/vendor/autoload.php';

kirby()->plugin('nerdcel/kirby3-blade-extensions', [
    'hooks' => require __DIR__ . '/src/hooks.php',

    'options' => require __DIR__ . '/src/config.php',
]);
