<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACP;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;

class AverageRating implements Metric\Config
{
    private Metric\Config\Meta $meta_metric;

    public function __construct(
        Metric\Config\Meta $meta_metric
    ) {
        $this->meta_metric = $meta_metric;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->meta_metric->create_formatter($aggregation_type);
    }

    public function create_query_bindings(AggregationType $aggregation_type): ACP\Query\Bindings
    {
        $bindings = $this->meta_metric->create_query_bindings($aggregation_type);
        $bindings->where('metric_meta_ac0.meta_value != 0');

        return $bindings;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->meta_metric->get_aggregation_types();
    }
}
