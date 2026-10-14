<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Customers extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            "
            LEFT JOIN {$wpdb->prefix}wc_order_product_lookup AS acsort_opl
                ON $wpdb->posts.ID = acsort_opl.product_id
            LEFT JOIN {$wpdb->prefix}wc_orders AS $alias
                ON acsort_opl.order_id = $alias.ID AND $alias.status = 'wc-completed'
        "
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("DISTINCT( $alias.customer_id )", (string)$order)
        );
    }

}
