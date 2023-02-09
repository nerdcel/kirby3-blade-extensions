<?php

use Nerdcel\BladeExtensions\PushCollector;

if (! function_exists('collectPush')) {
    function collectPush($scope, $data): void
    {
        PushCollector::getInstance()->collect($scope, $data);
    }
}

if (! function_exists('releasePush')) {
    function releasePush(): string
    {
        return PushCollector::getInstance()->release();
    }
}
