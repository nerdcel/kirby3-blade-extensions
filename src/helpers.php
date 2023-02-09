<?php

use Nerdcel\BladeExtensions\PushCollector;
use Nerdcel\BladeExtensions\StackCollector;

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

if (! function_exists('collectStack')) {
    function collectStack($scope, $data): void
    {
        StackCollector::getInstance()->collect($scope, $data);
    }
}

if (! function_exists('releaseStack')) {
    function releaseStack($scope): string
    {
        return StackCollector::getInstance()->release($scope);
    }
}
