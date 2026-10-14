<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\User;

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

        $select_column = $this->unique
            ? 'COUNT(DISTINCT p.product_id) AS productcount'
            : 'SUM(p.product_qty) AS productcount';

        $joins = sprintf(
            'LEFT JOIN %swc_orders as o ON o.customer_id = u.ID AND o.status IN %s LEFT JOIN %swc_order_product_lookup as p ON p.order_id = o.id AND p.product_qty > 0',
            $wpdb->prefix,
            $statuses_sql,
            $wpdb->prefix
        );

        $sub_query = QueryBuilder::create()
            ->select(sprintf('u.ID, %s', $select_column))
            ->from($wpdb->users, 'u')
            ->join_raw($joins)
            ->group_by('u.ID');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->users . '.ID', '=', $alias . '.ID')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast($alias . '.productcount', 'DECIMAL(10,2)'), '0');
    }

}
