<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Attachment extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->posts, $alias)
                ->on("$alias.post_parent", '=', "$wpdb->posts.ID")
                ->on("$alias.post_type", '=', "'attachment'")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.ID", (string)$order)
        );
    }

}
