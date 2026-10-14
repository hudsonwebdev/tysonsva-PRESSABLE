<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Comment;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\DateFormatter;
use ACP\Metric\Type\AggregationType;

class DateGmt extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_date();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DateFormatter($aggregation_type);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return $wpdb->comments . '.comment_date_gmt';
    }

}
