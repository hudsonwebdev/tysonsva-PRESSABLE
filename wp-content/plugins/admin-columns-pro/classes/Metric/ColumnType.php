<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Metric\Type\AggregationType;

interface ColumnType
{
    public function get_column_expression(string $column): string;

    public function get_aggregation_types(): AggregationTypes;

    public function create_formatter(AggregationType $aggregation_type): Formatter;

}
