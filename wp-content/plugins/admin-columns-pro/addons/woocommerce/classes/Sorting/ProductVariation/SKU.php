<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class SKU implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_sku = AliasGenerator::generate('sorting_sku');
        $alias_sku_parent = AliasGenerator::generate('sorting_sku_parent');

        $bindings->join(
            Join::left($wpdb->postmeta, $alias_sku)
                ->on("$alias_sku.post_id", '=', "$wpdb->posts.ID")
                ->on("$alias_sku.meta_key", '=', "'_sku'")
                ->get_sql()
            . "\n" .
            Join::inner($wpdb->posts, 'acsort_parent')
                ->on('acsort_parent.ID', '=', "$wpdb->posts.post_parent")
                ->on("acsort_parent.post_type", '=', "'product'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_sku_parent)
                ->on("$alias_sku_parent.post_id", '=', 'acsort_parent.ID')
                ->on("$alias_sku_parent.meta_key", '=', "'_sku'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "COALESCE( NULLIF( $alias_sku.meta_value, '' ), $alias_sku_parent.meta_value )",
                (string)$order,
                [
                    'esc_sql' => false,
                ]
            )
        );

        return $bindings;
    }

}
