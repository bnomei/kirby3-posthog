<?php

use Kirby\Cms\Collection;
use Kirby\Toolkit\Obj;

return function (?string $distinctId = null, ?array $groups = null): Collection {
    $featureFlags = posthog()->getAllFlags(
        $distinctId ?? site()->posthogDistinctId(),
        $groups ?? []
    );
    if ($featureFlags != null) {
        $featureFlags = array_map(function (string $item) {
            return new Obj([
                // needed for panel fields
                'text' => $item,
                'value' => $item,
                // needed for collection
                'id' => $item,
            ]);
        }, $featureFlags);
    }
    return new Collection($featureFlags ?? []);
};
