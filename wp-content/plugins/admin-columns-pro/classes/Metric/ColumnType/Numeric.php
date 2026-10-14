<?php

declare(strict_types=1);

namespace ACP\Metric\ColumnType;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\NullIf;

class Numeric implements ColumnType
{
    public function get_column_expression(string $column): string
    {
        return (string)new NullIf(new Cast($column, 'DECIMAL(10,2)'), '0');
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

}
