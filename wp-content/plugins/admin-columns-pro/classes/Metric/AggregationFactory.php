<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Metric\Type\AggregationType;
use LogicException;

final class AggregationFactory
{
    /**
     * @var array<string, class-string<Aggregation>>
     */
    private static array $mapping = [
        AggregationType::SUM        => Aggregation\Sum::class,
        AggregationType::MIN        => Aggregation\Min::class,
        AggregationType::MAX        => Aggregation\Max::class,
        AggregationType::AVG        => Aggregation\Average::class,
        AggregationType::RANGE      => Aggregation\Range::class,
        AggregationType::DATE_RANGE => Aggregation\DateRange::class,
        AggregationType::DATE_MIN   => Aggregation\Min::class,
        AggregationType::DATE_MAX   => Aggregation\Max::class,
    ];

    public static function create(AggregationType $aggregation_type): Aggregation
    {
        $value = $aggregation_type->get_value();

        switch ($value) {
            case AggregationType::COUNT:
                return new Aggregation\CountOccurrence((string)$aggregation_type->get_context());
            case AggregationType::COUNT_PERCENTAGE:
                return new Aggregation\CountOccurrence((string)$aggregation_type->get_context(), true);
            case AggregationType::IS_EMPTY:
                return new Aggregation\IsEmpty();
            case AggregationType::IS_EMPTY_PERCENTAGE:
                return new Aggregation\IsEmpty(true);
            case AggregationType::IS_NOT_EMPTY:
                return new Aggregation\IsNotEmpty();
            case AggregationType::IS_NOT_EMPTY_PERCENTAGE:
                return new Aggregation\IsNotEmpty(true);
        }

        if (isset(self::$mapping[$value])) {
            $class = self::$mapping[$value];

            return new $class();
        }

        throw new LogicException('Aggregation type not supported: ' . $value);
    }

}
