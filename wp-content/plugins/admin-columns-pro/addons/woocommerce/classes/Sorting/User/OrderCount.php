<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class OrderCount extends Base
{
    private array $status;

    public function __construct(array $status = [])
    {
        $this->status = $status;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join = Join::left("{$wpdb->prefix}wc_orders", $alias)
            ->on("$alias.customer_id", '=', "$wpdb->users.ID");

        if ($this->status) {
            $placeholders = implode(', ', array_fill(0, count($this->status), '%s'));
            $join->on("$alias.status", 'IN', '(' . $wpdb->prepare($placeholders, $this->status) . ')');
        }

        $bindings->join($join->get_sql());

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                sprintf('%s.%s', $alias, 'id'),
                (string)$order
            )
        );
    }

}
