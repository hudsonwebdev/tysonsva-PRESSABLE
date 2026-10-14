<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class OrderCount extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $subquery = "
            SELECT wcopl.product_id, COUNT(*) AS total_count
            FROM {$wpdb->prefix}wc_order_product_lookup AS wcopl
            JOIN {$wpdb->prefix}wc_orders AS wco
                ON wcopl.order_id = wco.ID
            GROUP BY wcopl.product_id
        ";

        $bindings->join(
            "LEFT JOIN ({$subquery}) AS {$alias} ON {$wpdb->posts}.ID = {$alias}.product_id"
        );
        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.total_count", (string)$order)
        );
    }

}
