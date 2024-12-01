<?php

use Kirby\Cms\Collection;
use Kirby\Toolkit\Obj;

return function (?string $distinctId = null, ?array $groups = null): Collection {
    $data = posthog()->getAllFlags(
        $distinctId ?? site()->posthogDistinctId(),
        $groups ?? []
    );
    $featureFlags = [];
    if ($data != null) {
        foreach ($data as $key => $value) {
            $featureFlags[] = new Obj([
                // needed for panel fields
                'text' => $key.' ['.t('posthog.enabled.'.$value, $value ? 'ENABLED' : 'DISABLED').']',
                'value' => $key,
                // needed for collection
                'id' => $key,
            ]);
        }
    }

    return new Collection($featureFlags);
};
