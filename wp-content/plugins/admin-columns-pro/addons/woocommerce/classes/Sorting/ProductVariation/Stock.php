<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class Stock implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_stock = AliasGenerator::generate('sorting_stock');
        $alias_stock_parent = AliasGenerator::generate('sorting_stock_parent');

        $bindings->join(
            Join::inner($wpdb->posts, 'acsort_parent')
                ->on('acsort_parent.ID', '=', "$wpdb->posts.post_parent")
                ->on("acsort_parent.post_type", '=', "'product'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_stock)
                ->on("$alias_stock.post_id", '=', "$wpdb->posts.ID")
                ->on("$alias_stock.meta_key", '=', "'_stock'")
                ->on("$alias_stock.meta_value", '<>', "''")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_stock_parent)
                ->on("$alias_stock_parent.post_id", '=', 'acsort_parent.ID')
                ->on("$alias_stock_parent.meta_key", '=', "'_stock'")
                ->on("$alias_stock_parent.meta_value", '<>', "''")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                "CAST( COALESCE( NULLIF( $alias_stock.meta_value, '' ), $alias_stock_parent.meta_value ) AS SIGNED )",
                (string)$order,
                [
                    'esc_sql' => false,
                ]
            )
        );

        return $bindings;
    }

}
