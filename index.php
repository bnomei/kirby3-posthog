<?php

@include_once __DIR__ . '/vendor/autoload.php';

if (!class_exists('Bnomei\Posthog')) {
    require_once './classes/Posthog.php';
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
]);
