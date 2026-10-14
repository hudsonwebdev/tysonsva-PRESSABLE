<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class PostParent extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->posts, $alias)
                ->on("$wpdb->posts.post_parent", '=', "$alias.ID")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.post_title", (string)$order)
        );
    }

}
