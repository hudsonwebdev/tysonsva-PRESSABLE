<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use DateTime;

class OrderTotal extends Base
{
    private array $statuses;

    private ?int $number_of_days;

    public function __construct(array $statuses = [], ?int $number_of_days = null)
    {
        $this->statuses = $statuses;
        $this->number_of_days = $number_of_days;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $status_statement = '';

        if (! empty($this->statuses)) {
            $status_statement = sprintf(
                "AND wco.status IN ('%s')",
                implode("','", array_map('esc_sql', $this->statuses))
            );
        }

        $date_statement = '';

        if ($this->number_of_days !== null) {
            $start_date = new DateTime();
            $start_date->modify('-' . $this->number_of_days . ' days');

            $date_statement = sprintf(
                "AND wco.date_created_gmt >= '%s'",
                $start_date->format('Y-m-d')
            );
        }

        $subquery = "
            SELECT wcopl.product_id, SUM(wcopl.product_net_revenue) AS total_revenue
            FROM {$wpdb->prefix}wc_order_product_lookup AS wcopl
            JOIN {$wpdb->prefix}wc_orders AS wco
                ON wcopl.order_id = wco.ID {$status_statement} {$date_statement}
            GROUP BY wcopl.product_id
        ";

        $bindings->join(
            "LEFT JOIN ({$subquery}) AS {$alias} ON {$wpdb->posts}.ID = {$alias}.product_id"
        );
        $bindings->order_by(
            SqlOrderByFactory::create("{$alias}.total_revenue", (string)$order)
        );
    }

}
