<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Response extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::inner($wpdb->posts, $alias)
                ->on("$alias.ID", '=', "$wpdb->comments.comment_post_ID")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.post_title ", (string)$order)
        );
    }

}
