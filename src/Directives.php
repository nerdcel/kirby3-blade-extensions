<?php

namespace Nerdcel\BladeExtensions;

use Nerdcel\BladeExtensions\Contracts\Registrable;
use Nerdcel\BladeExtensions\Traits\Singleton;
use Kirby\Toolkit\Str;
use MatthiasMullie\Minify;

class Directives extends Register implements Registrable
{
    use Singleton;

    public function __construct()
    {
        $this->key = 'directives';
    }

    public function list(): array
    {
        return [
            'siteTitle' => function () {
                return '<?= $site->title()->or("'.env('KIRBY_TITLE').'")->escape(); ?>';
            },

            'pageTitle' => function () {
                return '<?= $page->title()->or("")->escape(); ?>';
            },

            'dd' => function ($var, $json = false) {
                return "<?php $json? dump(json_decode($var)) : dump($var); die(); ?>";
            },

            'level' => function ($level = '1', $content = '') {
                return "<<?=$level?>><?=$content?></<?=$level?>>";
            },

            'vueData' => function (string $data, $scope = null) {
                $minify = new Minify\JS();
                $minify->add("window.AppData.setData($data, $scope);");

                return '<script type="text/javascript">'.$minify->minify().'</script>';
            },

            'vueMethod' => function (string $method, $scope = null) {
                $minify = new Minify\JS();
                $minify->add("window.AppData.setMethod($method, $scope);");

                return '<script type="text/javascript">'.$minify->minify().'</script>';
            },

            'inlineScript' => function () {
                return '<script type="text/javascript"><?php (function ($min) { $min->add("';
            },

            'endinlineScript' => function () {
                return '"); echo $min->minify(); })(new MatthiasMullie\Minify\JS()); ?></script>';
            },

            'meta' => function ($method) {
                if (method_exists(page()->meta(), $method)) {
                    return '<?= $page->meta()->'.$method.'(); ?>';
                }

                return '';
            },

            'vueAddPayload' => function () {
                [$scope, $data] = explode(',', func_get_args()[0]);

                if ($scope !== '') {
                    return <<<PHP
                        <?php collectPush($scope, $data); ?>
                    PHP;
                }
                return '';
            },

            'vuePayload' => function () {
                return '<script type="application/json" id="app-data"><?= releasePush() ?></script>';
            }
        ];
    }
}
