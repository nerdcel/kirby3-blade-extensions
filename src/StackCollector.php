<?php

namespace Nerdcel\BladeExtensions;

use Nerdcel\BladeExtensions\Traits\Singleton;
class StackCollector
{
    use Singleton;

    protected array $data = [];

    public function collect($scope, $data): void
    {
        $this->data[$scope][] = $data;
    }

    public function release($scope): string
    {
        if (array_key_exists($scope, $this->data)) {
            return implode(";", $this->data[$scope]);
        }
        return '';
    }
}
