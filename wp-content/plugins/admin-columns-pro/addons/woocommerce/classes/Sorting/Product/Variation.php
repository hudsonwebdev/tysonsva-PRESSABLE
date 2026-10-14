<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Variation extends Base implements WarningAware
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::for($wpdb->posts, 'ac_variation_count')
            ->select_columns(['ac_variation_count.ID', 'count(*) AS count', 'post_parent'])
            ->where('post_type', '=', 'product_variation')
            ->and_where('post_status', '=', 'publish')
            ->group_by('post_parent')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$alias.post_parent", '=', "$wpdb->posts.ID")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.count",
                (string)$order,
                [
                    'cast_type' => CastType::SIGNED,
                ]
            )
        );
    }

}
