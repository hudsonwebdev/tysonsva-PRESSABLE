<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Meta;

use AC\MetaType;
use ACP\Metric\AggregationTypes;

class Choice extends Text
{
    /**
     * @param array<string, string> $choices [value => label]
     */
    public static function from_choices(
        MetaType $meta_type,
        string $meta_key,
        array $choices
    ): ?self {
        /** @var self|null $config */
        $config = self::from_meta_type(
            $meta_type,
            $meta_key,
            AggregationTypes::for_choices($choices)->with_existence()
        );

        return $config;
    }

}
