<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

/**
 * Sort the comment list table on the number of times the meta_key is used by a comment.
 */
class MetaCount extends Base
{
    private string $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->commentmeta, $alias)
                ->on("$wpdb->comments.comment_ID", '=', "$alias.comment_id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
        );
        $bindings->group_by("$wpdb->comments.comment_ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.meta_key", (string)$order)
        );
    }

}
