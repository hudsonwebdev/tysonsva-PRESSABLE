<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Strategy;

use ACA\WC\Search\Query\OrderQueryController;
use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableQuery;

class Order extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        ob_start();
        add_filter('woocommerce_order_list_table_prepare_items_query_args', [$this, 'modify_orders_query']);
        add_filter('woocommerce_orders_table_query_clauses', function ($clauses, OrdersTableQuery $query, $args) use ($bindings) {
            if (! OrderQueryController::is_main_query($args)) {
                return $clauses;
            }

            $this->calculate_metric($clauses, $bindings);
            exit;
        }, 100, 3);
    }

    public function modify_orders_query(array $args): array
    {
        $args['return'] = 'ids';
        $args['paginate'] = false;
        $args['orderby'] = 'none';

        return $args;
    }

    private function calculate_metric(array $clauses, Bindings $bindings): void
    {
        global $wpdb;

        $orders_table = $wpdb->prefix . 'wc_orders';

        $inner_query = QueryBuilder::create()
            ->select($clauses['fields'])
            ->from($orders_table);

        if ($clauses['join']) {
            $inner_query->join_raw($clauses['join']);
        }

        if ($clauses['where']) {
            $inner_query->where_raw($clauses['where']);
        }

        $outer_query = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($inner_query->get_sql(), 'inner_query')
            ->join_raw(sprintf('INNER JOIN %s ON inner_query.id = %s.id', $orders_table, $orders_table));

        if ($bindings->get_join()) {
            $outer_query->join_raw($bindings->get_join());
        }

        if ($bindings->get_where()) {
            $outer_query->where_raw($bindings->get_where());
        }

        ob_end_clean();

        $this->send_response(
            (string)$wpdb->get_var($outer_query->get_sql())
        );
    }

}
