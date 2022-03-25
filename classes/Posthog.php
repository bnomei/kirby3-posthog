<?php

namespace Bnomei;

use Kirby\Toolkit\A;

/**
 * @method init(?string $apiKey = null, ?array $options = [], ?PosthogClient $client = null): void
 * @method capture(array $message)
 * @method identify(array $message)
 * @method groupIdentify(array $message)
 * @method isFeatureEnabled(string $key, string $distinctId, $default = false, array $groups = []): bool
 * @method fetchEnabledFeatureFlags(string $distinctId, array $groups = array()): array
 * @method alias(array $message)
 * @method raw(array $message)
 * @method validate($msg, $type)
 * @method flush()
 */
final class Posthog
{
    private ?PosthogClient $client = null;
    private bool $isEnabled;

    /**
     * @var array
     */
    private $options;

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
            'host' => option('bnomei.posthog.host'),
            'userid' => option('bnomei.posthog.userid'),
        ];
        $this->options = array_merge($defaults, $options);

        foreach ($this->options as $key => $call) {
            if (is_callable($call) && in_array($key, ['apikey', 'host', 'error_reporting'])) {
                $this->options[$key] = $call();
            }
        }

        if (kirby()->system()->isLocal() && $this->options['enabled'] !== 'force') {
            $this->options['enabled'] = false;
        } elseif ($this->options['enabled'] === 'force') {
            $this->options['enabled'] = true;
        }

        if ($this->options['enabled'] === false) {
            return; // do not creat a client
        }

        $this->client = new PosthogClient($this->options['apikey'], [
            'host' => $this->options['host'],
        ]);
        \PostHog\PostHog::init(null, [], $this->client);
    }

    public function option(?string $key = null)
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
        return $this->client;
    }

    public function __call(string $name, array $arguments)
    {
        if ($this->client && $this->options['enabled']) {
            return $this->client->{$name}(...$arguments);
        }

        return null;
    }

    private static $singleton = null;

    public static function singleton(array $options = []): self
    {
        if (!static::$singleton) {
            static::$singleton = new self($options);
        }
        return static::$singleton;
    }
}
