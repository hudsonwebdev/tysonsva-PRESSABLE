<?php

declare(strict_types=1);

namespace ACP\Metric\ColumnType;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Choice implements ColumnType
{
    private array $choices;

    private bool $allow_empty;

    public function __construct(array $choices, bool $allow_empty = false)
    {
        $this->choices = $choices;
        $this->allow_empty = $allow_empty;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        $types = AggregationTypes::for_choices($this->choices);

        return $this->allow_empty
            ? $types->with_existence()
            : $types;
    }

    public function get_column_expression(string $column): string
    {
        return $column;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }
}
