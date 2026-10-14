<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;

interface Config
{
    public function get_aggregation_types(): AggregationTypes;

    public function create_query_bindings(AggregationType $aggregation_type): Bindings;

    public function create_formatter(AggregationType $aggregation_type): Formatter;

}
