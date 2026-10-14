<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Revisions implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->join(
            Join::left($wpdb->posts, 'acs_p')
                ->on('acs_p.post_parent', '=', "$wpdb->posts.ID")
                ->on("acs_p.post_type", '=', "'revision'")
                ->get_sql()
        )
                               ->group_by("$wpdb->posts.ID")
                               ->order_by(SqlOrderByFactory::create_with_count("acs_p.ID", (string)$order));
    }

}
