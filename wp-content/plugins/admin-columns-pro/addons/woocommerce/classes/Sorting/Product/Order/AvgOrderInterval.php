<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class AvgOrderInterval extends Base
{
    private int $number_of_days;

    public function __construct(int $number_of_days = 365)
    {
        $this->number_of_days = $number_of_days;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $subquery = sprintf(
            "
            SELECT wcopl.product_id, %d / COUNT(*) AS avg_interval
            FROM %swc_order_product_lookup AS wcopl
            JOIN %swc_orders AS wco
                ON wcopl.order_id = wco.ID
                AND wco.status = 'wc-completed'
                AND wco.type = 'shop_order'
            GROUP BY wcopl.product_id
            ",
            $this->number_of_days,
            $wpdb->prefix,
            $wpdb->prefix
        );

        $bindings->join(
            "LEFT JOIN ({$subquery}) AS {$alias} ON {$wpdb->posts}.ID = {$alias}.product_id"
        );
        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.avg_interval", (string)$order)
        );
    }

}
