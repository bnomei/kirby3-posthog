<?php

namespace Bnomei;

use Kirby\Toolkit\A;
use PostHog\Client;

class PosthogClient extends Client
{
    // override and add caching
    public function localFlags()
    {
        // add caching
        $cacheKey = md5(__DIR__ . 'localFlags');
        $cache = kirby()->cache('bnomei.posthog')->get($cacheKey);
        if (!$cache) {
            $cache = parent::localFlags(); // json string or null
            kirby()->cache('bnomei.posthog')->set(
                $cacheKey,
                $cache,
                option('bnomei.posthog.featureflags', 1)
            );
        }
        return $cache;
    }
}
