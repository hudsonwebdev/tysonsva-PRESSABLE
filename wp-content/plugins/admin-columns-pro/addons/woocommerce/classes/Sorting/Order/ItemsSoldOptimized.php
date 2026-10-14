<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class ItemsSoldOptimized extends Base
{
    // This only works when Analytics feature in WooCommerce is enabled
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_order_stats", $alias)
                ->on("$alias.order_id", '=', "{$wpdb->prefix}wc_orders.id")
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.num_items_sold",
                (string)$order,
                [
                    'empty_values' => [EmptyValues::NULL, EmptyValues::ZERO],
                ]
            )
        );
    }

}
