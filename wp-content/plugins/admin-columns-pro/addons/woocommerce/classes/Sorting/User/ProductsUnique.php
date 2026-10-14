<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class ProductsUnique implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_order = AliasGenerator::generate('sorting_order');
        $alias_product = AliasGenerator::generate('sorting_order_product_lookup');

        $statuses = array_map(static fn ($s) => 'wc-' . $s, wc_get_is_paid_statuses());
        $placeholders = implode(', ', array_fill(0, count($statuses), '%s'));
        $statuses_in = '(' . $wpdb->prepare($placeholders, $statuses) . ')';

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_orders", $alias_order)
                ->on("$alias_order.customer_id", '=', "$wpdb->users.ID")
                ->on("$alias_order.status", 'IN', $statuses_in)
                ->get_sql()
            . "\n" .
            Join::left("{$wpdb->prefix}wc_order_product_lookup", $alias_product)
                ->on("$alias_product.order_id", '=', "$alias_order.id")
                ->get_sql()
        );

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                sprintf('%s.%s', $alias_product, 'product_id'),
                (string)$order
            )
        );

        return $bindings;
    }

}
