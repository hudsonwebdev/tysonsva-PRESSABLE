<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class TotalPaid extends Metric\Config\Base
{
    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $order_table = $wpdb->prefix . 'wc_orders';

        $subquery = QueryBuilder::create()
            ->select('orders.id, orders.total_amount + COALESCE(SUM(refunds.total_amount), 0) as total_paid')
            ->from($order_table, 'orders')
            ->join_raw(
                sprintf(
                    'LEFT JOIN %s AS refunds ON refunds.parent_order_id = orders.id AND refunds.type = "shop_order_refund"',
                    $order_table
                )
            )
            ->where('orders.type', '=', 'shop_order')
            ->group_by('orders.id');

        $bindings->join(
            Join::left_subquery($subquery->get_sql(), $alias)
                ->on("{$order_table}.id", '=', "{$alias}.id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Cast("{$alias}.total_paid", 'DECIMAL(10,2)');
    }

}
