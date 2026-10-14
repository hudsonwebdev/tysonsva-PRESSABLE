<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Visibility extends Metric\Config\Base
{
    private function get_choices(): array
    {
        return [
            'private'   => _x('Private', 'post status'),
            'protected' => _x('Password protected', 'post status'),
            'public'    => __('Public'),
        ];
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices($this->get_choices());
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return sprintf(
            "CASE WHEN %s.post_status = 'private' THEN 'private' WHEN %s.post_password != '' THEN 'protected' ELSE 'public' END",
            $wpdb->posts,
            $wpdb->posts
        );
    }

}
