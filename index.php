<?php

@include_once __DIR__ . '/vendor/autoload.php';

if (!class_exists('Bnomei\Posthog')) {
    require_once __DIR__ . '/classes/PosthogClient.php';
    require_once __DIR__ . '/classes/Posthog.php';
}

if (!function_exists('posthog')) {
    function posthog()
    {
        return \Bnomei\Posthog::singleton();
    }
}

Kirby::plugin('bnomei/posthog', [
    'options' => [
        'enabled' => true,
        'userid' => 'id', // or email
        'apikey' => fn () => null,
        'host' => fn () => 'https://app.posthog.com',
        'error_reporting' => false,
        'cache' => true,
    ],
    'blueprints' => [
        'fields/posthog-abtests' => __DIR__ . '/blueprints/fields/posthog-abtests.yml',
        'fields/posthog-feature-flags' => __DIR__ . '/blueprints/fields/posthog-feature-flags.yml',
    ],
    'collections' => [
        'posthogFeatureFlags' => require __DIR__ . '/collections/posthogFeatureFlags.php',
    ],
    'snippets' => [
        'posthog' => __DIR__ . '/snippets/script-posthog.php',
    ],
    'pageMethods' => [
        'posthogCapturePageView' => function (?string $distinctId = null, array $properties = []) {
            $url =  $this->url();
            $event = '$pageview';

            if ($this->intendedTemplate() == 'error') {
                if (posthog()->option('error_reporting')) {
                    $url = url(kirby()->request()->path());
                    $event = t('posthog.page-not-found', 'page not found');
                } else {
                    return null; // exit
                }
            }

            return posthog()->capture([
                'distinctId' => $distinctId ?? site()->kirbyUserId(),
                'event' => $event,
                'properties' => array_merge([
                    '$current_url' => $url,
                ], $properties),
            ]);
        },
    ],
    'siteMethods' => [
        'kirbyUserId' => function (): ?string {
            $field = option('bnomei.posthog.userid');
            return kirby()->user() ? kirby()->user()->{$field}() : null;
        },
        'posthogFeatureFlags' => function (?string $distinctId = null, array $groups = []): \Kirby\Cms\Collection {
            return kirby()->collection('posthogFeatureFlags')($distinctId, $groups);
        },
        'posthogABTest' => function ($page) {
            if (!$page || $page->abtests()->isEmpty()) {
                return null;
            }
            $distinctId = posthog()->identify([]); // TODO: message
            foreach ($page->abtests()->toStructure() as $test) {
                if (posthog()->isFeatureEnabled(
                    (string) $test->posthogfeatureflag(),
                    $distinctId
                )) {
                    if ($bpage = $test->showbpage()->toPage()) {
                        return $bpage;
                    }
                }
            }
            return null;
        }
    ],
    'routes' => [
        [
            'pattern' => '(:all)',
            'action'  => function ($id) {
                if (posthog()->isEnabled() && $bpage = site()->posthogABTest(page($id))) {
                    return site()->visit($bpage);
                }
                return $this->next();
            }
        ]
    ],
]);
