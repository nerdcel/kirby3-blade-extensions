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
        return implode(";", $this->data[$scope]);
    }
}
