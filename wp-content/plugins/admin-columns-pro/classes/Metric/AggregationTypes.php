<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC\TypedArrayIterator;
use ACP\Metric\Type\AggregationType;

final class AggregationTypes extends TypedArrayIterator
{
    private const EXISTENCE_TYPES = [
        AggregationType::IS_EMPTY,
        AggregationType::IS_EMPTY_PERCENTAGE,
        AggregationType::IS_NOT_EMPTY,
        AggregationType::IS_NOT_EMPTY_PERCENTAGE,
    ];

    public function __construct(array $aggregations = [])
    {
        parent::__construct($aggregations, AggregationType::class);
    }

    public function current(): AggregationType
    {
        return current($this->data);
    }

    public static function create_from_array(array $aggregations): self
    {
        return new self(
            array_map(
                static function (string $aggregation) {
                    return new AggregationType($aggregation);
                },
                $aggregations
            )
        );
    }

    public static function for_date(): self
    {
        return self::create_from_array([
            AggregationType::DATE_RANGE,
            AggregationType::DATE_MIN,
            AggregationType::DATE_MAX,
        ]);
    }

    public static function for_numeric(): self
    {
        return self::create_from_array([
            AggregationType::SUM,
            AggregationType::MIN,
            AggregationType::MAX,
            AggregationType::AVG,
            AggregationType::RANGE,
        ]);
    }

    public static function for_existence(): self
    {
        return self::create_from_array(self::EXISTENCE_TYPES);
    }

    /**
     * @param array<int|string, string> $choices [value => label]
     */
    public static function for_choices(array $choices): self
    {
        $types = [];

        foreach ($choices as $value => $label) {
            $types[] = new AggregationType(AggregationType::COUNT, (string)$value, $label);
        }

        foreach ($choices as $value => $label) {
            $types[] = new AggregationType(AggregationType::COUNT_PERCENTAGE, (string)$value, $label);
        }

        return (new self($types));
    }

    public function merge(self $other): self
    {
        return new self(
            array_merge($this->get_copy(), $other->get_copy())
        );
    }

    public function with_existence(): self
    {
        return $this->merge(self::for_existence());
    }

}
