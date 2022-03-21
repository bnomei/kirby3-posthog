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
        'apikey' => fn () => null,
        'host' => fn () => null,
        'cache' => true,
    ],
    'blueprints' => [
        'fields/posthog-abtests' => __DIR__ . '/blueprints/fields/posthog-abtests.yml',
        'fields/posthog-feature-flags' => __DIR__ . '/blueprints/fields/posthog-feature-flags.yml',
    ],
    'collections' => [
        'posthogFeatureFlags' => require __DIR__ . '/collections/posthogFeatureFlags.php',
    ],
    'siteMethods' => [
        'posthogFeatureFlags' => function (?string $distinctId = null, array $groups = []) {
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
                if ($bpage = site()->posthogABTest(page($id))) {
                    return site()->visit($bpage);
                }
                return $this->next();
            }
        ]
    ],
]);
