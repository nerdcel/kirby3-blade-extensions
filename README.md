# Kirby3 plugin: Blade extensions

This plugin provides custom blade directives/ifs for your Kirby project, when using the leitsch/blade kirby3 plugin.
To set your own custom directives/ifs, please see the [Options](#options) section in this README file.

> This plugin is completely free and published under the MIT license. However, if you are using it in a commercial project and want to help me keep up with maintenance, please consider [making a donation of your choice](https://www.paypal.me/nerdcel).

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby3-blade-extensions`.

### Git submodule

```
git submodule add https://github.com/nerdcel/kirby3-blade-extensions.git site/plugins/kirby3-blade-extensions
```

### Composer

```
composer require nerdcel/kirby3-blade-extensions
```

## Available directives

- siteTitle
- pageTitle
- dd
- level
- vueData
- vueMethod
- inlineScript (endinlineScript)
- meta

## Available ifs

- viteDevMode

## Available components

- vueComponent

## Options

The following options are available to be set using your site/config/config.php

```php
'nerdcel.kirby3-blade-extensions' => [
    'directives' => ['name' => method(), ...],
    'ifs' => ['name' => method(), ...]
]
```

If that doesn't work, rund ```npm install``` first.

## License

MIT

## Credits

- [Marcel Hieke](https://github.com/nerdcel)
