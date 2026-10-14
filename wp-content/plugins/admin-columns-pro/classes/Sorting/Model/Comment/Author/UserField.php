<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment\Author;

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
            Join::left($wpdb->users, $alias)
                ->on("$wpdb->comments.user_id", '=', "$alias.ID")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.$this->user_field", (string)$order)
        );
    }

}
