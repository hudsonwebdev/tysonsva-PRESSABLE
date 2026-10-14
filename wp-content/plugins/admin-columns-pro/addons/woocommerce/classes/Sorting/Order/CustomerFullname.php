<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class CustomerFullname extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_customer = $wpdb->prefix . 'wc_customer_lookup';

        $bindings->join(
            Join::left($table_customer, $alias)
                ->on("$alias.user_id", '=', "$table_orders.customer_id")
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                [
                    "$alias.first_name",
                    "$alias.last_name",
                ],
                (string)$order
            )
        );
    }

}
