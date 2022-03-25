<?php

use Kirby\Toolkit\Obj;

return function (?string $distinctId = null, ?array $groups = null) {
    $featureFlags = posthog()->fetchEnabledFeatureFlags(
        $distinctId ?? site()->kirbyUserId(),
        $groups ?? []
    );
    if ($featureFlags != null) {
        $featureFlags = array_map(function (string $item) {
            return new Obj([
                'text' => $item,
                'value' => $item,
            ]);
        }, $featureFlags);
    }
    return $featureFlags ?? [];
};
