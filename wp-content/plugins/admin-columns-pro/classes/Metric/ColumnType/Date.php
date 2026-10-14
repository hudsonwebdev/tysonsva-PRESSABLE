<?php

declare(strict_types=1);

namespace ACP\Metric\ColumnType;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Formatter\DateFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Sql\Expression\Conditional\NullIf;

class Date implements ColumnType
{
    public function get_column_expression(string $column): string
    {
        return (string)new NullIf($column, "'0000-00-00 00:00:00'");
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_date()->with_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DateFormatter($aggregation_type);
    }

}
