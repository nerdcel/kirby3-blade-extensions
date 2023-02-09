<?php

namespace Nerdcel\BladeExtensions;

use Nerdcel\BladeExtensions\Traits\Singleton;
class PushCollector
{
    use Singleton;

    protected array $data = [];

    public function collect($scope, $data): void
    {
        $this->data[$scope] = $data;
    }

    public function release(): string
    {
        try {
            return json_encode($this->data, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return '';
        }
    }
}
