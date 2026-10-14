<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class Dimensions implements WarningAware, QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_length = AliasGenerator::generate('sorting_length');
        $alias_width = AliasGenerator::generate('sorting_width');
        $alias_height = AliasGenerator::generate('sorting_height');

        $bindings->join(
            Join::left($wpdb->postmeta, $alias_length)
                ->on("$alias_length.post_id", '=', "$wpdb->posts.ID")
                ->on("$alias_length.meta_key", '=', "'_length'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_width)
                ->on("$alias_width.post_id", '=', "$wpdb->posts.ID")
                ->on("$alias_width.meta_key", '=', "'_width'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_height)
                ->on("$alias_height.post_id", '=', "$wpdb->posts.ID")
                ->on("$alias_height.meta_key", '=', "'_height'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                "$alias_length.meta_value * $alias_width.meta_value * $alias_height.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}
