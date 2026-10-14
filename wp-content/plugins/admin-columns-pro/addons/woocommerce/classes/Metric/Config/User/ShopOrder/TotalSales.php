<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User\ShopOrder;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class TotalSales extends Metric\Config\Base
{
    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $sub_query = QueryBuilder::create()
            ->select('pm_customer.meta_value AS user_id')
            ->add_select('SUM(pm_total.meta_value) AS totalsales')
            ->from($wpdb->posts, 'o')
            ->join_raw(sprintf(
                "INNER JOIN %s AS pm_customer ON pm_customer.post_id = o.ID AND pm_customer.meta_key = '_customer_user'",
                $wpdb->postmeta
            ))
            ->join_raw(sprintf(
                "INNER JOIN %s AS pm_total ON pm_total.post_id = o.ID AND pm_total.meta_key = '_order_total'",
                $wpdb->postmeta
            ))
            ->where('o.post_type', '=', 'shop_order')
            ->and_where_raw('o.post_status IN ' . $statuses_sql)
            ->group_by('pm_customer.meta_value')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("{$wpdb->users}.ID", '=', "{$alias}.user_id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast($alias . '.totalsales', 'DECIMAL(10,2)'), '0');
    }

}
