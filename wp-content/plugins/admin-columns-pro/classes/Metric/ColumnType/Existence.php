<?php

declare(strict_types=1);

namespace ACP\Metric\ColumnType;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Existence implements ColumnType
{
    public function get_column_expression(string $column): string
    {
        return $column;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

}
