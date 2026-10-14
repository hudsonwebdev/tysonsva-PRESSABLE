<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\ShopOrder;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Subtotal extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::create()
            ->select('oi.order_id AS order_id')
            ->add_select('SUM(im.meta_value) AS subtotal')
            ->from($wpdb->prefix . 'woocommerce_order_items', 'oi')
            ->join_raw(sprintf(
                "INNER JOIN %swoocommerce_order_itemmeta AS im ON im.order_item_id = oi.order_item_id AND im.meta_key = '_line_subtotal'",
                $wpdb->prefix
            ))
            ->where('oi.order_item_type', '=', 'line_item')
            ->group_by('oi.order_id')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.order_id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast("{$alias}.subtotal", 'DECIMAL(10,2)'), '0');
    }

}
