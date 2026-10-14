<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Meta;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Config\Meta;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Text extends Meta
{
    protected function get_column_expression(string $alias): string
    {
        return sprintf('%s.meta_value', $alias);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->metrics ?? AggregationTypes::for_existence();
    }

    protected function create_default_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

}
