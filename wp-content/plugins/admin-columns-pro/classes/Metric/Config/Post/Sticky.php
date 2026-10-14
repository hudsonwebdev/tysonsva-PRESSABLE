<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Sticky extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        $choices = [
            '1' => __('Sticky', 'codepress-admin-columns'),
            '0' => __('Not Sticky', 'codepress-admin-columns'),
        ];

        return AggregationTypes::for_choices($choices);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        $stickies = get_option('sticky_posts', []);
        $sticky_ids = is_array($stickies) ? array_filter(array_map('intval', $stickies)) : [];

        if ($sticky_ids) {
            return sprintf(
                "CASE WHEN %s.ID IN (%s) THEN '1' ELSE '0' END",
                $wpdb->posts,
                implode(',', $sticky_ids)
            );
        }

        return "'0'";
    }

}
