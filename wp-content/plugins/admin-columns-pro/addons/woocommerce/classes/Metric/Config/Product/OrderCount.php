<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class OrderCount extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric()->with_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::create()
            ->select('product_id')
            ->add_select('COUNT(order_id) AS order_count')
            ->from($wpdb->prefix . 'wc_order_product_lookup')
            ->group_by('product_id');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->posts . '.ID', '=', $alias . '.product_id')
                ->get_sql()
        );

        $bindings->group_by('NULL');
        $bindings->order_by('NULL');
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.order_count';
    }

}
