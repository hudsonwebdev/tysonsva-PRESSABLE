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

class Refunds extends Metric\Config\Base
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
            ->select('refund.post_parent AS order_id')
            ->add_select('SUM(rm.meta_value) AS refunded')
            ->from($wpdb->posts, 'refund')
            ->join_raw(sprintf(
                "INNER JOIN %s AS rm ON rm.post_id = refund.ID AND rm.meta_key = '_refund_amount'",
                $wpdb->postmeta
            ))
            ->where('refund.post_type', '=', 'shop_order_refund')
            ->group_by('refund.post_parent')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.order_id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast("{$alias}.refunded", 'DECIMAL(10,2)'), '0');
    }

}
