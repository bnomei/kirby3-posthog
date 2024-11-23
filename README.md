# Kirby Posthog

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-posthog?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby3-posthog?color=272822)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Kirby Plugin for connecting Kirby to [Posthog](https://posthog.com/)

## Install

Using composer:

```bash
composer require bnomei/kirby3-posthog
```

Using git submodules:

```bash
git submodule add https://github.com/bnomei/kirby3-posthog.git site/plugins/kirby3-posthog
```

Using download & copy: download [the latest release](https://github.com/bnomei/kirby3-posthog/releases) and copy to `site/plugins`

## Setup

You can set the apikey and host in the config.

**site/config/config.php**
```php
return [
    // other config settings ...
    'bnomei.posthog.apikey' => 'YOUR-KEY-HERE',
    'bnomei.posthog.host' => 'YOUR-HOST-HERE',
];
```

You can also set a callback if you use the [dotenv Plugin](https://github.com/bnomei/kirby3-dotenv).

**site/config/config.php**
```php
return [
    // other config settings ...
    'bnomei.posthog.apikey' => function() { return env('POSTHOG_APIKEY'); },
    'bnomei.posthog.host' => function() { return env('POSTHOG_HOST'); },
];
```

### Javascript

Output the tracking Javascript via the snippet included in the plugin.

```php
  <?php snippet('posthog'); ?>
  </body>
</html>
```

## Usage

### PHP track pageview

**site/template/default.php**
```php
<?php
// track page view event for current kirby user or identified posthog user
$page->posthogCapturePageView();

// be careful to not have any whitespace before <html>
><html>
    <!-- ... --->
</html>
```

### PHP helper function

Use the `posthog()`-helper to access Posthog. You can use all methods from the [Posthog PHP library](https://github.com/PostHog/posthog-php).

```php
<?php

posthog()->capture([
    'distinctId' => site()->posthogDistinctId(),
    'event' => 'movie played',
    'properties' => array(
        'movieId' => '123',
        'category' => 'romcom'
    )
])
```

> NOTE: Using the static class from the official posthog docs is not supported.

In addition to the `posthog()`-helper this plugin adds the following features to the original library.

- Disabled on localhost by default
- Cache for Feature Flag list - it would otherwise send a http request to your posthog instance every time you access the list. It still will send one for every feature flag check.

## Settings

| bnomei.posthog. | Default                   | Description                                                 |
|-----------------|---------------------------|-------------------------------------------------------------|
| apikey          | `string or callback`      |                                                             |
| personalapikey  | `string or callback`      |                                                             |
| host            | `string or callback`      |                                                             |
| enabled         | `true or false or 'force'` | but disabled on localhost setups by default                 |
| featureflags    | `1`                       | duration (in minutes) to cache the feature flags in minutes |

> Read more about `apikey` and `personalapikey` here: https://posthog.com/docs/api

## Dependencies

- [Posthog PHP](https://github.com/PostHog/posthog-php)

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-posthog/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

