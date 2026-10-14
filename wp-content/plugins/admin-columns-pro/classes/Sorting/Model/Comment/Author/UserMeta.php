<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class UserMeta extends Base
{
    private string $meta_field;

    public function __construct(string $meta_field)
    {
        $this->meta_field = $meta_field;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->usermeta, $alias)
                ->on("$wpdb->comments.user_id", '=', "$alias.user_id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_field))
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );
    }

}
