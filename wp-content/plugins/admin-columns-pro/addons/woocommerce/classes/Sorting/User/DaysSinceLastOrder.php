<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class DaysSinceLastOrder extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::for("{$wpdb->prefix}wc_orders")
            ->select_columns(['customer_id', 'MAX(date_created_gmt) AS last_order_date'])
            ->where('type', '=', 'shop_order')
            ->and_where_in('status', ['wc-completed', 'wc-processing'])
            ->group_by('customer_id')
            ->get_sql();

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$wpdb->users.ID", '=', "$alias.customer_id")
                ->get_sql()
        );

        // "Days since" ASC = most recent first = date DESC, and vice versa
        $reversed = (string)$order === 'ASC' ? 'DESC' : 'ASC';

        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.last_order_date", $reversed, [
                'cast_type'    => CastType::create_from_data_type(new DataType(DataType::DATETIME))->get_value(),
                'empty_values' => [null],
            ])
        );
    }

}
