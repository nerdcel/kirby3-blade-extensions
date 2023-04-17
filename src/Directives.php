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

            'dd' => function ($expression) {
                return "<?php
                    \$directive_arguments = [{$expression}];

                    if (count(\$directive_arguments) === 2) {
                        \$var = \$directive_arguments[0];
                        \$json = \$directive_arguments[1];
                    } else {
                        [\$var] = \$directive_arguments;
                        \$json = false;
                    }
                    \$json? dump(json_decode(\$var)) : dump(\$var);
                    unset(\$var, \$json, \$directive_arguments);
                    die();
                ?>";
            },

            'level' => function ($expression) {
                return "<?php
                    \$directive_arguments = [{$expression}];

                    if (count(\$directive_arguments) === 2) {
                        \$directive_level = \$directive_arguments[0];
                        \$directive_content = \$directive_arguments[1];
                    } else {
                        [\$directive_level] = \$directive_arguments;
                        \$directive_content = \"\";
                    }

                    echo \"<\$directive_level>\$directive_content</\$directive_level>\";
                ?>";
            },

            'vueData' => function ($expression) {
                return "<?php
                    \$directive_arguments = [{$expression}];

                    if (count(\$directive_arguments) === 2) {
                        \$directive_data = \$directive_arguments[0];
                        \$directive_scope = \"'\" . \$directive_arguments[1] . \"'\";
                    } else {
                        [\$directive_data] = \$directive_arguments;
                        \$directive_scope = null;
                    }

                    \$minify = new MatthiasMullie\Minify\JS();
                    \$minify->add(\"window.AppData.setData(\" . json_encode(\$directive_data) . \", \$directive_scope);\");

                    collectStack(\"vue\", \$minify->minify());
                    unset(\$directive_data, \$directive_scope, \$directive_arguments);
                ?>";
            },

            'vueMethod' => function ($expression) {
                return "<?php
                    \$directive_method = \"$expression\";
                    \$minify = new MatthiasMullie\Minify\JS();
                    \$minify->add(\"window.AppData.setMethod(\$directive_method);\");

                    collectStack(\"vue\", \$minify->minify());
                    unset(\$directive_method, \$directive_scope, \$directive_arguments);
                ?>";
            },

            'vuePayload' => function () {
                return '<script type="application/json" id="app-data"><?= releasePush() ?></script>';
            },

            'vueRelease' => function () { // TODO use window load to release VUE data
                return '<script type="text/javascript">window.addEventListener("AppDataInit", function() {
                    <?= releaseStack("vue"); ?>;
                    window.AppData.kirby = window.AppData.kirby || {};
                    window.AppData.kirby.language = {
                        code: "<?= $kirby->language()->code() ?>",
                        name: "<?= $kirby->language()->name() ?>",
                        direction: "<?= $kirby->language()->direction() ?>",
                        url: "<?= $kirby->language()->url() ?>"
                    };
                });</script>';
            },

            'inlineScript' => function () {
                return '<script type="text/javascript"><?php (function ($min) { $min->add("';
            },

            'endinlineScript' => function () {
                return '"); echo $min->minify(); })(new MatthiasMullie\Minify\JS()); ?></script>';
            },

            'meta' => function ($expression) {
                return "<?php
                    \$directive_arguments = [\"$expression\"];
                    [\$directive_method] = \$directive_arguments;
                    if (method_exists(page()->meta(),\$directive_method)) {
                        echo \$page->meta()->{\$directive_method}();
                    }
                    unset(\$directive_arguments, \$directive_method);
                ?>";
            },

            'vueAddPayload' => function ($expression) {
                return "<?php
                    \$directive_arguments = [{$expression}];

                    if (count(\$directive_arguments) === 2) {
                        \$directive_scope = \"\$directive_arguments[0]\";
                        \$directive_data = \$directive_arguments[1];
                    } else {
                        [\$directive_scope] = \$directive_arguments;
                        \$directive_data = null;
                    }

                    if (\$directive_scope !== '') {
                        collectPush(\$directive_scope, \$directive_data);
                    }

                    unset(\$directive_arguments, \$directive_scope, \$directive_data);
                ?>";
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

            'externalLinksJs' => function ($expression = null) {
                return "<?php
                if (!array_key_exists('nerdcel/kirby3-external-links', kirby()->plugins())) {
                    echo '';
                } else {
                    \$directive_arguments = [{$expression}];

                    if (count(\$directive_arguments) >= 1) {
                        [\$directive_path] = \$directive_arguments;
                    } else {
                        \$directive_path = null;
                    }

                    if (!\$directive_path) {
                        \$directive_path = 'media/plugins/nerdcel/kirby3-external-links/index.js';
                    }
                    echo htmlentities(externalLinksJs(\$directive_path, page()));
                }
                ?>";
            },

            'cache' => function($expression) {
                return "<?php
                    \$__cache_directive_arguments = [{$expression}];
                    if (count(\$__cache_directive_arguments) === 2) {
                        [\$__cache_directive_key, \$__cache_directive_ttl] = \$__cache_directive_arguments;
                    } else {
                        [\$__cache_directive_key] = \$__cache_directive_arguments;
                        \$__cache_directive_ttl = option('nerdcel.kirby3-blade-extensions.inline-cache.ttl', 0);
                    }

                    if ((\$__cache = kirby()->cache('nerdcel.kirby3-blade-extensions.inline-cache')) && (\$value = \$__cache->get(\$__cache_directive_key)) !== null) {
                        echo \$value;
                    } else {
                        \$__cache_directive_buffering = true;
                        ob_start();
                ?>";
            },

            'endcache' => function() {
                return "<?php
                        \$__cache_directive_buffer = ob_get_clean();
                        \$__cache->set(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);
                        echo \$__cache_directive_buffer;
                        unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments, \$__cache);
                    }
                ?>";
            },

            't' => function ($expression) {
                return "<?php
                    \$directive_arguments = [{$expression}];
                    if (count(\$directive_arguments) === 2) {
                        [\$directive_key, \$directive_fallback] = \$directive_arguments;
                    } else {
                        [\$directive_key] = \$directive_arguments;
                        \$directive_fallback = \$directive_key;
                    }

                    echo t(\$directive_key, \$directive_fallback);
                    unset(\$directive_key, \$directive_fallback, \$directive_arguments);
                ?>";
            },
        ];
    }
}
