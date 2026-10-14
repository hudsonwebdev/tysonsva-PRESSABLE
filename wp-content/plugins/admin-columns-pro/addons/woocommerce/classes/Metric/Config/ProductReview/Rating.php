<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\ProductReview;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Config\Meta\Numeric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;

class Rating implements Metric\Config
{
    private Numeric $config;

    public function __construct()
    {
        $this->config = Numeric::for_comment(
            'rating',
            AggregationTypes::create_from_array([
                AggregationType::AVG,
                AggregationType::MIN,
                AggregationType::MAX,
            ])
        );
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->config->get_aggregation_types();
    }

    public function create_query_bindings(AggregationType $aggregation_type): Bindings
    {
        return $this->config->create_query_bindings($aggregation_type);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->config->create_formatter($aggregation_type);
    }

}
