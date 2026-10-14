<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class TotalSales extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $statuses = array_map(static fn ($s) => 'wc-' . $s, wc_get_is_paid_statuses());
        $placeholders = implode(', ', array_fill(0, count($statuses), '%s'));
        $statuses_in = '(' . $wpdb->prepare($placeholders, $statuses) . ')';

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_orders", $alias)
                ->on("$alias.customer_id", '=', "$wpdb->users.ID")
                ->on("$alias.status", 'IN', $statuses_in)
                ->get_sql()
        );

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                sprintf('%s.%s', $alias, 'total_amount'),
                (string)$order
            )
        );
    }

}
