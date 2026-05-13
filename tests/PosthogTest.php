<?php

use Bnomei\Posthog;
use Bnomei\PosthogClient;
use Kirby\Cms\App;
use PostHog\HttpResponse;

function posthogTestResetSingleton(): void
{
    $property = new ReflectionProperty(Posthog::class, 'singleton');
    $property->setValue(null, null);
}

function posthogTestApp(array $options = []): App
{
    App::destroy();
    App::$enableWhoops = false;
    posthogTestResetSingleton();

    require dirname(__DIR__).'/index.php';

    return new App([
        'roots' => [
            'index' => dirname(__DIR__).'/tests',
        ],
        'options' => array_merge([
            'debug' => false,
            'bnomei.posthog.enabled' => 'force',
            'bnomei.posthog.apikey' => 'ph_project',
            'bnomei.posthog.personalapikey' => null,
        ], $options),
    ]);
}

afterEach(function () {
    App::destroy();
    posthogTestResetSingleton();
});

it('builds pageview capture data from the current page', function () {
    $kirby = posthogTestApp();
    $page = $kirby->site()->find('home');

    expect($page)->not->toBeNull();

    $data = $page->posthogCapturePageView('visitor-1', [
        'source' => 'test',
    ], true);

    expect($data)->toBeArray();
    expect($data['distinctId'])->toBe('visitor-1');
    expect($data['event'])->toBe('$pageview');
    expect($data['send_feature_flags'])->toBeTrue();
    expect($data['properties'])->toHaveKey('$current_url');
    expect($data['properties']['source'])->toBe('test');
});

it('returns an http response from cached local flags', function () {
    posthogTestApp();

    $client = posthog()->client();
    expect($client)->toBeInstanceOf(PosthogClient::class);

    $class = new ReflectionClass(PosthogClient::class);
    $cacheKey = md5(dirname($class->getFileName()).'localFlags');
    kirby()->cache('bnomei.posthog')->set($cacheKey, '{"flags":[]}', 1);

    $response = $client->localFlags();

    expect($response)->toBeInstanceOf(HttpResponse::class);
    expect($response->getResponseCode())->toBe(200);
    expect($response->getResponse())->toBe('{"flags":[]}');
});
