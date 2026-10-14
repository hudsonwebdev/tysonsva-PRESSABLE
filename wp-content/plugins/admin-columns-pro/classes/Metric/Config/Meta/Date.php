<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Meta;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Config\Meta;
use ACP\Metric\Formatter\DateFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Sql\Expression\Cast;

class Date extends Meta
{
    private bool $is_timestamp = false;

    public function as_timestamp(): self
    {
        $clone = clone $this;
        $clone->is_timestamp = true;

        return $clone;
    }

    protected function get_column_expression(string $alias): string
    {
        $column = $alias . '.meta_value';

        if ($this->is_timestamp) {
            $column = sprintf('FROM_UNIXTIME(%s)', $column);
        }

        return (string)new Cast($column, 'DATETIME');
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->metrics ?? AggregationTypes::for_date()->with_existence();
    }

    protected function create_default_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DateFormatter($aggregation_type);
    }

}
