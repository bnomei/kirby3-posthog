<?php

namespace Bnomei;

use Exception;
use ReflectionClass;

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

    public function __construct()
    {
        $this->isEnabled = option('bnomei.posthog.enabled') !== false;
        if (kirby()->system()->isLocal() && option('bnomei.posthog.enabled') !== 'force') {
            $this->isEnabled = false;
        }
        if ($this->isEnabled === false) {
            return;
        }

        $apikey = option('posthog.apikey');
        $apikey = !is_string($apikey) && is_callable($apikey) ? $apikey() : $apikey;
        $host = option('posthog.host');
        $host = !is_string($host) && is_callable($host) ? $host() : $host;
        $options = [];
        if (!empty($host)) {
            $options = [
                'host' => $host, // You can remove this line if you're using app.posthog.com
            ];
        }
        $this->client = new PosthogClient($apikey, $options);
        \PostHog\PostHog::init(null, [], $this->client);
    }

    public function client(): ?PosthogClient
    {
        return $this->client;
    }

    public function __call(string $name, array $arguments)
    {
        if ($this->client && $this->isEnabled) {
            return $this->client::{$name}($arguments);
        }

        return null;
    }

    private static $singleton = null;

    public static function singleton(): self
    {
        if (!static::$singleton) {
            static::$singleton = new self();
        }
        return static::$singleton;
    }
}
