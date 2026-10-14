<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Description extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->term_taxonomy, $alias)
                ->on('t.term_id', '=', "$alias.term_id")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.description",
                (string)$order
            )
        );
    }

}
