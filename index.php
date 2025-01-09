<?php

use Kirby\Toolkit\A;

@include_once __DIR__.'/vendor/autoload.php';

if (! class_exists('Bnomei\Posthog')) {
    require_once __DIR__.'/classes/PosthogClient.php';
    require_once __DIR__.'/classes/Posthog.php';
}

if (! function_exists('posthog')) {
    function posthog(): \Bnomei\Posthog
    {
        return \Bnomei\Posthog::singleton();
    }
}

Kirby::plugin('bnomei/posthog', [
    'options' => [
        'enabled' => true,
        'distinctId' => function () {
            $kirby = kirby();
            if ($kirby->user()) {
                return $kirby->user()->id();
            }

            // ph_XXX_posthog -> distinct_id
            if ($sessionCookie = A::get($_COOKIE, 'ph_'.posthog()->option('apikey').'_posthog', null)) {
                $sessionCookie = urldecode($sessionCookie);
                $sessionCookie = json_decode($sessionCookie, true);
                if ($distinctId = A::get($sessionCookie, 'distinct_id', null)) {
                    return $distinctId;
                }
            }

            $session = $kirby->session()->token();
            if (empty($session)) {
                $kirby->session()->regenerateToken();
                $session = $kirby->session()->token();
            }

            return sha1(__DIR__.$session);
        },
        'apikey' => fn () => null,
        'personalapikey' => fn () => null,
        'host' => fn () => 'https://app.posthog.com',
        'error_reporting' => false,
        'cache' => true,
    ],
    'blueprints' => [
        'fields/posthog-abtests' => __DIR__.'/blueprints/fields/posthog-abtests.yml',
        'fields/posthog-feature-flags' => __DIR__.'/blueprints/fields/posthog-feature-flags.yml',
    ],
    'collections' => [
        'posthogFeatureFlags' => require __DIR__.'/collections/posthogFeatureFlags.php',
    ],
    'snippets' => [
        'posthog' => __DIR__.'/snippets/script-posthog.php',
    ],
    'pageMethods' => [
        'posthogCapturePageViewData' => function (?string $distinctId = null, array $properties = []): ?array {
            if (posthog()->isEnabled() === false) {
                return null;
            }

            $url = $this->url();
            $event = '$pageview';

            if ($this->intendedTemplate() == 'error') {
                if (posthog()->option('error_reporting')) {
                    $url = url(kirby()->request()->path());
                    $event = t('posthog.page-not-found', 'page not found');
                } else {
                    return null; // exit
                }
            }

            $sessionId = null;
            // ph_XXX_posthog -> $sesid.1
            if ($sessionCookie = A::get($_COOKIE, 'ph_'.posthog()->option('apikey').'_posthog', null)) {
                $sessionCookie = urldecode($sessionCookie);
                $sessionCookie = json_decode($sessionCookie, true);
                if ($sessionId = A::get($sessionCookie, '$sesid', null)) {
                    if (is_array($sessionId) && count($sessionId) > 1) {
                        $sessionId = $sessionId[1];
                    }
                }
            }

            return [
                'distinctId' => $distinctId ?? site()->posthogDistinctId(),
                'event' => $event,
                'send_feature_flags' => true, // https://posthog.com/docs/libraries/php#method-2-set-send_feature_flags-to-true
                'properties' => array_merge(array_filter([
                    '$current_url' => $url,
                    '$session_id' => $sessionId,
                ]), $properties),
            ];
        },
        'posthogCapturePageView' => function (?string $distinctId = null, array $properties = [], bool $return = false): null|bool|array {
            if (posthog()->isEnabled() === false) {
                return null;
            }

            $data = site()->posthogCapturePageViewData($distinctId, $properties); // @phpstan-ignore-line

            return $return ? $data : posthog()->capture($data);
        },
    ],
    'siteMethods' => [
        'posthogDistinctId' => function (): ?string {
            return option('bnomei.posthog.distinctId')(); // @phpstan-ignore-line
        },
        'posthogFeatureFlags' => function (?string $distinctId = null, array $groups = []): \Kirby\Cms\Collection {
            return kirby()->collection('posthogFeatureFlags')($distinctId, $groups); // @phpstan-ignore-line
        },
        'posthogABTest' => function (?\Kirby\Cms\Page $page) {
            if (! $page || $page->abtests()->isEmpty()) { // @phpstan-ignore-line
                return null;
            }
            $distinctId = site()->posthogDistinctId();
            foreach ($page->abtests()->toStructure() as $test) { // @phpstan-ignore-line
                if ($test->posthogvariant()->isNotEmpty()) {
                    $variant = strval($test->posthogvariant()->value());
                    if ($variant === posthog()->getFeatureFlag(
                        (string) $test->posthogfeatureflag(),
                        $distinctId
                    )) {
                        if ($bpage = $test->showbpage()->toPage()) {
                            return $bpage;
                        }
                    }
                } else {
                    if (posthog()->isFeatureEnabled(
                        (string) $test->posthogfeatureflag(),
                        $distinctId
                    )) {
                        if ($bpage = $test->showbpage()->toPage()) {
                            return $bpage;
                        }
                    }
                }

            }

            return null;
        },
    ],
    'routes' => [
        [
            'pattern' => '(:all)',
            'action' => function ($id) {
                if (posthog()->isEnabled() && $bpage = site()->posthogABTest(page($id))) {
                    return site()->visit($bpage);
                }

                return $this->next();
            },
        ],
    ],
]);
