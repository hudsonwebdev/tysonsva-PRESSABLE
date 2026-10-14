<?php

declare(strict_types=1);

namespace ACA\EC\Metric\Config\Event;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Config\Base;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Sticky extends Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices([
            '1' => __('Sticky', 'codepress-admin-columns'),
            '0' => __('Not Sticky', 'codepress-admin-columns'),
        ]);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return sprintf(
            "CASE WHEN %s.menu_order = -1 THEN '1' ELSE '0' END",
            $wpdb->posts
        );
    }

}
