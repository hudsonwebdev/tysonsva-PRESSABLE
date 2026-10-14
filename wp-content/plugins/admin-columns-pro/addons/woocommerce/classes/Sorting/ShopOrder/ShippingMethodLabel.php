<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class ShippingMethodLabel extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left("{$wpdb->prefix}woocommerce_order_items", $alias)
                ->on("$wpdb->posts.ID", '=', "$alias.order_id")
                ->on("$alias.order_item_type", '=', "'shipping'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.order_item_name", (string)$order)
        );
    }

}
