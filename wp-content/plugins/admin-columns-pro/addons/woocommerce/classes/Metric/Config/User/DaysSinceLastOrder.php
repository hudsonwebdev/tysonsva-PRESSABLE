<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User;

use ACA\WC\Metric\Formatter\DaysFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\DateDiff;
use ACP\Sql\Expression\Greatest;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class DaysSinceLastOrder extends Metric\Config\Base
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

        $sub_query = QueryBuilder::create()
            ->select('customer_id')
            ->add_select(
                new Greatest(
                    new DateDiff('NOW()', 'MAX(date_created_gmt)'),
                    '0'
                ) . ' AS days_since_last_order'
            )
            ->from($wpdb->prefix . 'wc_orders')
            ->where('type', '=', 'shop_order')
            ->and_where_in('status', ['wc-completed', 'wc-processing'])
            ->group_by('customer_id');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->users . '.ID', '=', $alias . '.customer_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.days_since_last_order';
    }

}
