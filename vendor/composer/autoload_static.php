<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1df4a2b2646419cbe9194a98caa4dcdb
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PostHog\\' => 8,
        ),
        'K' => 
        array (
            'Kirby\\' => 6,
        ),
        'B' => 
        array (
            'Bnomei\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PostHog\\' => 
        array (
            0 => __DIR__ . '/..' . '/posthog/posthog-php/lib',
        ),
        'Kirby\\' => 
        array (
            0 => __DIR__ . '/..' . '/getkirby/composer-installer/src',
        ),
        'Bnomei\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $classMap = array (
        'Bnomei\\Posthog' => __DIR__ . '/../..' . '/classes/Posthog.php',
        'Bnomei\\PosthogClient' => __DIR__ . '/../..' . '/classes/PosthogClient.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Kirby\\ComposerInstaller\\CmsInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/CmsInstaller.php',
        'Kirby\\ComposerInstaller\\Installer' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Installer.php',
        'Kirby\\ComposerInstaller\\Plugin' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Plugin.php',
        'Kirby\\ComposerInstaller\\PluginInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/PluginInstaller.php',
        'PostHog\\Client' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Client.php',
        'PostHog\\Consumer' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Consumer.php',
        'PostHog\\Consumer\\File' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Consumer/File.php',
        'PostHog\\Consumer\\ForkCurl' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Consumer/ForkCurl.php',
        'PostHog\\Consumer\\LibCurl' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Consumer/LibCurl.php',
        'PostHog\\Consumer\\Socket' => __DIR__ . '/..' . '/posthog/posthog-php/lib/Consumer/Socket.php',
        'PostHog\\FeatureFlag' => __DIR__ . '/..' . '/posthog/posthog-php/lib/FeatureFlag.php',
        'PostHog\\HttpClient' => __DIR__ . '/..' . '/posthog/posthog-php/lib/HttpClient.php',
        'PostHog\\HttpResponse' => __DIR__ . '/..' . '/posthog/posthog-php/lib/HttpResponse.php',
        'PostHog\\InconclusiveMatchException' => __DIR__ . '/..' . '/posthog/posthog-php/lib/InconclusiveMatchException.php',
        'PostHog\\PostHog' => __DIR__ . '/..' . '/posthog/posthog-php/lib/PostHog.php',
        'PostHog\\QueueConsumer' => __DIR__ . '/..' . '/posthog/posthog-php/lib/QueueConsumer.php',
        'PostHog\\SizeLimitedHash' => __DIR__ . '/..' . '/posthog/posthog-php/lib/SizeLimitedHash.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1df4a2b2646419cbe9194a98caa4dcdb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1df4a2b2646419cbe9194a98caa4dcdb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1df4a2b2646419cbe9194a98caa4dcdb::$classMap;

        }, null, ClassLoader::class);
    }
}
