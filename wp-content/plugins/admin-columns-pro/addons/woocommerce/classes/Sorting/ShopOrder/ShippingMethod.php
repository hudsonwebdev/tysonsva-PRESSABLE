<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class ShippingMethod extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left("{$wpdb->prefix}woocommerce_order_items", 'acsort_oi')
                ->on("$wpdb->posts.ID", '=', 'acsort_oi.order_id')
                ->on("acsort_oi.order_item_type", '=', "'shipping'")
                ->get_sql()
            . "\n" .
            Join::left("{$wpdb->prefix}woocommerce_order_itemmeta", $alias)
                ->on('acsort_oi.order_item_id', '=', "$alias.order_item_id")
                ->on("$alias.meta_key", '=', "'method_id'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );
    }

}
