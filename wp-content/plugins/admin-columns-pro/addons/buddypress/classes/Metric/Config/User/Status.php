<?php

declare(strict_types=1);

namespace ACA\BP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class Status extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices([
            '0' => __('Active', 'buddypress'),
            '1' => __('Spammer', 'buddypress'),
        ]);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return sprintf('%s.user_status', $wpdb->users);
    }

}
