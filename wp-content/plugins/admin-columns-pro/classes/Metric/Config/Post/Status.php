<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Status extends Metric\Config\Base
{
    private array $statuses;

    public function __construct(array $statuses)
    {
        $this->statuses = $statuses;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices($this->statuses);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return sprintf('%s.post_status', $wpdb->posts);
    }

}
