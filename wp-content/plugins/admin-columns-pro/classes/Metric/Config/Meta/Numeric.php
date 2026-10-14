<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Meta;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Config\Meta;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Sql\Expression\Cast;

class Numeric extends Meta
{
    protected function get_column_expression(string $alias): string
    {
        return (string)new Cast($alias . '.meta_value', 'DECIMAL(10,2)');
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->metrics ?? AggregationTypes::for_numeric()->with_existence();
    }

    protected function create_default_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

}
