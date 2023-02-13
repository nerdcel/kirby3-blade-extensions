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

                return '<?php collectStack("vue", "'.$minify->minify().'"); ?>';
            },

            'vueMethod' => function (string $method, $scope = null) {
                $minify = new Minify\JS();
                $minify->add("window.AppData.setMethod($method, $scope);");

                return '<?php collectStack("vue", "'.$minify->minify().'"); ?>';
            },

            'vueRelease' => function () { // TODO use window load to release VUE data
                return '<script type="text/javascript">window.addEventListener("AppDataInit", function() {<?= releaseStack("vue") ?>;});</script>';
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
            },

            'attributes' => function ($attributes) {
                if (preg_match('/\[{1}.*\]{1}/ms', $attributes, $matches)) {
                    $cleaned = preg_replace("/\r|\n|\[|\]/", "", $matches[0]);
                    $snippetArgs = array_map('trim', explode(",", $cleaned));
                    $snippetArgs = rtrim(implode(",\n", $snippetArgs), ',');
                    $attributes = preg_replace('/\[{1}.*\]{1}/ms', '['.$snippetArgs.']', $attributes);
                }

                return '<?php
                    $attrs = '.$attributes.';
                    foreach($attrs as $attr => $cond) {
                        echo $cond ? " " . $attr : "";
                    }
                ?>';
            },

            'colorTheme' => function () {
                return '<?= $site->theme()->or("light")->value(); ?>';
            },

            'autoTheme' => function () {
                return '<?php if ($site->theme()->or("auto")->value() === "auto"): ?>
                    <script>
                        if (localStorage.theme === "dark" || (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
                            document.documentElement.classList.add("dark");
                            document.documentElement.classList.remove("light");
                        }
    
                        if (localStorage.theme === "light" || (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: light)").matches)) {
                            document.documentElement.classList.add("light");
                            document.documentElement.classList.remove("dark");
                        }
    
                        if (!("theme" in localStorage) && !window.matchMedia("(prefers-color-scheme: dark)").matches && !window.matchMedia("(prefers-color-scheme: light)").matches) {
                            document.documentElement.classList.remove("light");
                            document.documentElement.classList.remove("dark");
                            document.documentElement.classList.add("auto");
                        }
                    </script>
                <?php endif; ?>';
            },

            'themeColors' => function () {
                $colors = site()->themecolors()->toStructure();
                $cssVars = [];

                foreach ($colors as $color) {
                    $cssVars[] = '--theme-'.Str::slug($color->name()).': '.join(' ',
                            array_filter($color->color()->toValues(), function ($key) {
                                return array_key_exists($key, ['r' => 0, 'g' => 0, 'b' => 0]);
                            }, ARRAY_FILTER_USE_KEY));
                }

                return '<style>:root,:before,:after{'.implode(';', $cssVars).(count($cssVars) ? ';' : '').'}</style>';
            },

            'headcode' => function () {
                return '<?= $site->headcode()->or(null)->value(); ?>';
            },

            'footercode' => function () {
                return '<?= $site->footercode()->or(null)->value(); ?>';
            },

            'externalLinksJs' => function ($path = null) {
                if (!array_key_exists('nerdcel/kirby3-external-links', kirby()->plugins())) {
                    return '';
                }
                if (!$path) {
                    $path = 'media/plugins/nerdcel/kirby3-external-links/index.js';
                }

                return "<?php echo htmlentities(externalLinksJs(\"$path\", page())); ?>";
            }
        ];
    }
}
