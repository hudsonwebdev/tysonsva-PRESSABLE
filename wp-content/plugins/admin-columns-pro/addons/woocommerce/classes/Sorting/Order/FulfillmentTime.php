<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Scheme\Orders;
use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Expression\Round;
use ACP\Sql\Expression\TimestampDiff;
use ACP\Sql\Join;

class FulfillmentTime extends Base
{
    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $table_orders = $wpdb->prefix . Orders::TABLE;
        $table_od = $wpdb->prefix . OrderOperationalData::TABLE;

        $bindings->join(
            Join::left($table_od, $alias)
                ->on("$alias.order_id", '=', "$table_orders.id")
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                (string)new Round(
                    new TimestampDiff(
                        TimestampDiff::SECOND,
                        "$table_orders.date_created_gmt",
                        "$alias.date_completed_gmt"
                    ) . ' / 86400.0'
                ),
                (string)$order,
                [
                    'empty_values' => [null],
                    'esc_sql'      => false,
                    'quote'        => false,
                ]
            )
        );
    }

}
