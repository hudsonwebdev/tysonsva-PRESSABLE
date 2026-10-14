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

class RefundAmount extends Metric\Config\Base
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
            ->select('parent_order_id, -SUM(total_amount) as refund_amount')
            ->from($order_table)
            ->where('type', '=', 'shop_order_refund')
            ->group_by('parent_order_id');

        $bindings->join(
            Join::left_subquery($subquery->get_sql(), $alias)
                ->on("{$alias}.parent_order_id", '=', "{$order_table}.id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Cast("{$alias}.refund_amount", 'DECIMAL(10,2)');
    }

}
