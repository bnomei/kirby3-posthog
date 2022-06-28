# Kirby 3 Posthog

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-posthog?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby3-posthog?color=272822)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-posthog)](https://travis-ci.com/bnomei/kirby3-posthog)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-posthog)](https://coveralls.io/github/bnomei/kirby3-posthog)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby3-posthog)](https://codeclimate.com/github/bnomei/kirby3-posthog)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Kirby 3 Plugin for connecting Kirby to [Posthog](https://posthog.com/)

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

## Commerical Usage

> <br>
> <b>Support open source!</b><br><br>
> This plugin is free but if you use it in a commercial project please consider to sponsor me or make a donation.<br>
> If my work helped you to make some cash it seems fair to me that I might get a little reward as well, right?<br><br>
> Be kind. Share a little. Thanks.<br><br>
> &dash; Bruno<br>
> &nbsp;

| M | O | N | E | Y |
|---|----|---|---|---|
| [Github sponsor](https://github.com/sponsors/bnomei) | [Patreon](https://patreon.com/bnomei) | [Buy Me a Coffee](https://buymeacoff.ee/bnomei) | [Paypal dontation](https://www.paypal.me/bnomei/15) | [Hire me](mailto:b@bnomei.com?subject=Kirby) |

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

## Usage

### Javascript

Output the tracking Javascript via the snippet included in the plugin.

```php
  <?php snippet('posthog'); ?>
  </body>
</html>
```

### PHP

Use the `posthog()`-helper to access Posthog. You can use all methods from the [Posthog PHP library](https://github.com/PostHog/posthog-php).

```php
posthog()->capture([
    // your capture data
])
```

## Additional Features

In addition to the `posthog()`-helper this plugin adds the following features to the original library.

- Disabled on localhost by default
- Cache for Feature Flags - it would send a http request every time you access one otherwise.

## Settings

| bnomei.posthog.    | Default                   | Description                                                 |
|--------------------|---------------------------|-------------------------------------------------------------|
| apikey             | `string or callback`      |                                                             |
| host               | `string or callback`      |                                                             |
| enabled            | `true or false or 'force'` | but disabled on localhost setups by default                 |
| featureflags | `1`                       | duration (in minutes) to cache the feature flags in minutes |

## Dependencies

- [Posthog PHP](https://github.com/PostHog/posthog-php)

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-posthog/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

