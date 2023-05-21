<?php

namespace Bnomei;

use Kirby\Toolkit\A;
use PostHog\Client;

class PosthogClient extends Client
{
    // override and add caching
    public function getAllFlags(string $distinctId, array $groups = array(), array $personProperties = array(), array $groupProperties = array(), bool $onlyEvaluateLocally = false): array
    {
        // add caching
        $cacheKey = md5($distinctId . implode('', $groups));
        $cache = kirby()->cache('bnomei.posthog')->get($cacheKey);
        if (!$cache) {
            $cache = json_decode($this->decide($distinctId, $groups), true) ?? [];
            kirby()->cache('bnomei.posthog')->set(
                $cacheKey,
                $cache,
                option('bnomei.posthog.featureflags', 1)
            );
        }
        return A::get($cache, 'featureFlags', []);
    }
}
