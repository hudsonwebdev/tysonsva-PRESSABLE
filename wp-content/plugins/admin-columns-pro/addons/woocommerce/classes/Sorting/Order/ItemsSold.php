<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class ItemsSold extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $sub_query = "SELECT order_id, SUM(product_qty) AS total_product_count
            FROM {$wpdb->prefix}wc_order_product_lookup GROUP BY order_id";

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$alias.order_id", '=', "{$wpdb->prefix}wc_orders.id")
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.total_product_count",
                (string)$order,
                [
                    'empty_values' => [EmptyValues::NULL, EmptyValues::ZERO],
                ]
            )
        );
    }

}
