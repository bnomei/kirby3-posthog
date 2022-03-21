<?php

namespace Bnomei;

use Kirby\Toolkit\A;
use PostHog\Client;

class PosthogClient extends Client
{
    /**
     * @inheritDoc
     */
    public function fetchEnabledFeatureFlags(string $distinctId, array $groups = array()): array
    {
        // add caching
        $cacheKey = md5($distinctId . implode('', $groups));
        $cache = kirby()->cache('bnomei.posthog')->get($cacheKey);
        if (!$cache) {
            $cache = json_decode($this->decide($distinctId, $groups), true);
            kirby()->cache('bnomei.posthog')->set($cacheKey, $cache, option('bnomei.posthog.featureflags', 1));
        }

        return A::get($cache, 'featureFlags', []);
    }
}
