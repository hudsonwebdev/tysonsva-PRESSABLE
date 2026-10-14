<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class AverageOrderValue extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $statuses = array_map(static fn ($s) => 'wc-' . $s, wc_get_is_paid_statuses());

        $sub_query = QueryBuilder::for("{$wpdb->prefix}wc_orders")
            ->select_columns(['customer_id', 'SUM(total_amount) / COUNT(*) AS avg_value'])
            ->where_in('status', $statuses)
            ->group_by('customer_id')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$wpdb->users.ID", '=', "$alias.customer_id")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.avg_value", (string)$order)
        );
    }

}
