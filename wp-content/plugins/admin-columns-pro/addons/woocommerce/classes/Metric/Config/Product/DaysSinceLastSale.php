<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACA\WC\Metric\Formatter\DaysFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\DateDiff;
use ACP\Sql\Expression\Greatest;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class DaysSinceLastSale extends Metric\Config\Base
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
            ->select('opl.product_id')
            ->add_select(
                new Greatest(
                    new DateDiff('NOW()', 'MAX(o.date_created_gmt)'),
                    '0'
                ) . ' AS days_since_last_sale'
            )
            ->from($wpdb->prefix . 'wc_orders', 'o')
            ->join($wpdb->prefix . 'wc_order_product_lookup', 'o.id', '=', 'opl.order_id', 'opl')
            ->where('o.type', '=', 'shop_order')
            ->and_where('o.status', '=', 'wc-completed')
            ->group_by('opl.product_id');

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
        return $alias . '.days_since_last_sale';
    }

}
