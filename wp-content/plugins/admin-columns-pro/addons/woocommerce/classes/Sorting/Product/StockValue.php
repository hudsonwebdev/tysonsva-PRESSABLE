<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class StockValue implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_pml = AliasGenerator::generate('sorting_product_meta_lookup');
        $alias_var = AliasGenerator::generate('sorting_variation_stock');

        $variation_sub_query = QueryBuilder::for($wpdb->posts, 'p')
            ->select_columns([
                'p.post_parent AS parent_id',
                'SUM(CAST(pml.min_price AS DECIMAL(10,2)) * CAST(pml.stock_quantity AS SIGNED)) AS total_stock_value',
            ])
            ->join("{$wpdb->prefix}wc_product_meta_lookup", 'pml.product_id', '=', 'p.ID', 'pml')
            ->where('p.post_type', '=', 'product_variation')
            ->and_where_raw('p.post_parent > 0')
            ->and_where_raw('pml.stock_quantity > 0')
            ->and_where_raw('pml.min_price > 0')
            ->group_by('p.post_parent')
            ->get_sql();

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_product_meta_lookup", $alias_pml)
                ->on("$alias_pml.product_id", '=', "$wpdb->posts.ID")
                ->get_sql()
            . "\n" .
            Join::left_subquery($variation_sub_query, $alias_var)
                ->on("$alias_var.parent_id", '=', "$wpdb->posts.ID")
                ->get_sql()
        );

        $expression = sprintf(
            "COALESCE(%s.total_stock_value, CAST(%s.min_price AS DECIMAL(10,2)) * CAST(%s.stock_quantity AS SIGNED))",
            $alias_var,
            $alias_pml,
            $alias_pml
        );

        $bindings->order_by(
            sprintf(
                "CASE WHEN %s IS NULL OR %s <= 0 THEN 1 ELSE 0 END, %s %s",
                $expression,
                $expression,
                $expression,
                (string)$order
            )
        );

        return $bindings;
    }

}
