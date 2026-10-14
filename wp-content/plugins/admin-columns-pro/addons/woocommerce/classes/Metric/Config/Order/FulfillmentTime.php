<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACA\WC\Metric\Formatter\DaysFormatter;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Scheme\Orders;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\DateDiff;
use ACP\Sql\Join;

class FulfillmentTime extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new DaysFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->prefix . OrderOperationalData::TABLE, $alias)
                ->on($wpdb->prefix . Orders::TABLE . '.id', '=', $alias . '.order_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return (string)new DateDiff(
            $alias . '.' . OrderOperationalData::DATE_COMPLETED_GMT,
            $wpdb->prefix . Orders::TABLE . '.' . Orders::DATE_CREATED_GMT
        );
    }

}
