<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

/**
 * Sort a user list table on the number of times the meta_key is used by a user.
 */
class MetaCount extends Base
{
    protected string $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->postmeta, $alias)
                ->on("$wpdb->posts.ID", '=', "$alias.post_id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.meta_key", (string)$order)
        );
    }

}
