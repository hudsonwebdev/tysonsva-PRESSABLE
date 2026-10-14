<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class UserField extends Base
{
    private string $user_field;

    public function __construct(string $user_field)
    {
        $this->user_field = $user_field;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::inner($wpdb->users, $alias)
                ->on("$wpdb->posts.post_author", '=', "$alias.ID")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                sprintf("$alias.%s", $this->user_field),
                (string)$order
            )
        );
    }

}
