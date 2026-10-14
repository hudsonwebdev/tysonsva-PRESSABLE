<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class ItemCount implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = AliasGenerator::generate('quantity');

        $bindings->join(
            Join::left("{$wpdb->prefix}woocommerce_order_items", 'acsort_order_items')
                ->on("$wpdb->posts.ID", '=', 'acsort_order_items.order_id')
                ->on("acsort_order_items.order_item_type", '=', "'line_item'")
                ->get_sql()
            . "\n" .
            Join::left("{$wpdb->prefix}woocommerce_order_itemmeta", $alias)
                ->on("$alias.order_item_id", '=', 'acsort_order_items.order_item_id')
                ->on("$alias.meta_key", '=', "'_qty'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                "$alias.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}
