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

class Products extends Metric\Config\Base
{
    private bool $unique;

    public function __construct(bool $unique = false)
    {
        $this->unique = $unique;
    }

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

        $meta_key = $this->unique ? '_product_id' : '_qty';
        $select_column = $this->unique
            ? 'COUNT(DISTINCT im.meta_value) AS productcount'
            : 'SUM(im.meta_value) AS productcount';

        $sub_query = QueryBuilder::create()
            ->select('pm_customer.meta_value AS user_id')
            ->add_select($select_column)
            ->from($wpdb->posts, 'o')
            ->join_raw(sprintf(
                "INNER JOIN %s AS pm_customer ON pm_customer.post_id = o.ID AND pm_customer.meta_key = '_customer_user'",
                $wpdb->postmeta
            ))
            ->join_raw(sprintf(
                "INNER JOIN %swoocommerce_order_items AS oi ON oi.order_id = o.ID AND oi.order_item_type = 'line_item'",
                $wpdb->prefix
            ))
            ->join_raw(sprintf(
                "INNER JOIN %swoocommerce_order_itemmeta AS im ON im.order_item_id = oi.order_item_id AND im.meta_key = '%s'",
                $wpdb->prefix,
                $meta_key
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
        return (string)new Coalesce(new Cast($alias . '.productcount', 'DECIMAL(10,2)'), '0');
    }

}
