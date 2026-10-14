<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class ProductCount extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $subquery = QueryBuilder::create()
            ->select('order_id, SUM(product_qty) as product_count')
            ->from($wpdb->prefix . 'wc_order_product_lookup')
            ->group_by('order_id')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($subquery, $alias)
                ->on($alias . '.order_id', '=', $wpdb->prefix . 'wc_orders.id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.product_count';
    }

}
