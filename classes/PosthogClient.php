<?php

namespace Bnomei;

use Kirby\Http\Remote;
use PostHog\Client;
use PostHog\HttpResponse;
use PostHog\PostHog;

class PosthogClient extends Client
{
    // override and add caching
    public function localFlags(): HttpResponse
    {
        // add caching
        $cacheKey = md5(__DIR__.'localFlags');
        $cache = kirby()->cache('bnomei.posthog')->get($cacheKey);
        if ($cache) {
            return new HttpResponse($cache, 200);
        }

        // parent::localFlags() does not seem fetch properly. so will use the kirby remote class
        $url = rtrim(strval(posthog()->option('host')), '/').
            '/api/feature_flag/local_evaluation?send_cohorts&token='.
            rawurlencode(strval(posthog()->option('apikey')));
        $response = Remote::get($url, [
            'headers' => [
                // Send user agent in the form of {library_name}/{library_version} as per RFC 7231.
                'User-Agent: posthog-php/'.PostHog::VERSION,
                'Authorization: Bearer '.strval(posthog()->option('personalapikey')),
            ],
        ]);
        $cache = $response->code() === 200 ? $response->content() : '';
        kirby()->cache('bnomei.posthog')->set(
            $cacheKey,
            $cache,
            intval(option('bnomei.posthog.featureflags', 1))
        );

        return new HttpResponse($response->content(), $response->code());
    }
}
