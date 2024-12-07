<?php

namespace Bnomei;

use Kirby\Toolkit\A;

/**
 * @method bool capture(array $message)
 * @method string|null getFeatureFlag(string $key, string $distinctId)
 * @method string|null isFeatureEnabled(string $key, string $distinctId)
 */
class Posthog
{
    private ?PosthogClient $client = null;

    private array $options;

    public function __construct(array $options = [])
    {
        if (option('debug')) {
            kirby()->cache('bnomei.posthog')->flush();
        }

        $defaults = [
            'debug' => option('debug'),
            'enabled' => option('bnomei.posthog.enabled'),
            'error_reporting' => option('bnomei.posthog.error_reporting'),
            'apikey' => option('bnomei.posthog.apikey'),
            'personalapikey' => option('bnomei.posthog.personalapikey'),
            'host' => option('bnomei.posthog.host'),
            'userid' => option('bnomei.posthog.userid'),
        ];
        $this->options = array_merge($defaults, $options);

        foreach ($this->options as $key => $call) {
            if (is_callable($call) && in_array($key, ['apikey', 'personalapikey', 'host', 'error_reporting'])) {
                $this->options[$key] = $call();
            }
        }

        if (kirby()->system()->isLocal() && $this->options['enabled'] !== 'force') {
            $this->options['enabled'] = false;
        } elseif ($this->options['enabled'] === 'force') {
            $this->options['enabled'] = true;
        }
    }

    private function getClient(): ?PosthogClient
    {
        if ($this->isEnabled() && ! $this->client) {
            // create client (Bnomei version with caching for feature flags)
            $this->client = new PosthogClient(
                $this->options['apikey'],
                ['host' => $this->options['host']],
                null,
                $this->options['personalapikey']
            );
            \PostHog\PostHog::init(null, [], $this->client);
        }

        return $this->client;
    }

    public function option(?string $key = null): mixed
    {
        if ($key) {
            return A::get($this->options, $key);
        }

        return $this->options;
    }

    public function isEnabled(): bool
    {
        return $this->options['enabled'];
    }

    public function client(): ?PosthogClient
    {
        return $this->getClient();
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($this->isEnabled() && $this->getClient()) {
            return $this->getClient()->{$name}(...$arguments);
        }

        return null;
    }

    private static ?self $singleton = null;

    public static function singleton(array $options = []): self
    {
        if (self::$singleton === null) {
            self::$singleton = new self($options);
        }

        return self::$singleton;
    }
}
