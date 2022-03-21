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
        'fields/posthog-feature-flags' => __DIR__ . '/blueprints/fields/posthog-feature-flags.yml',
    ],
    'collections' => [
        'posthogFeatureFlags' => require __DIR__ . '/collections/posthogFeatureFlags.php',
    ],
    'siteMethods' => [
        'posthogFeatureFlags' => function(?string $distinctId = null, array $groups = []) {
            return kirby()->collection('posthogFeatureFlags')($distinctId, $groups);
        },
    ],
]);
