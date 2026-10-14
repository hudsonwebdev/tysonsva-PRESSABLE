<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use InvalidArgumentException;

class OrderExtrema extends Base
{
    private string $extrema;

    private array $status;

    public function __construct(string $extrema = 'min', array $status = ['wc-completed'])
    {
        $this->extrema = $extrema;
        $this->status = $status;
        $this->validate();
    }

    private function validate(): void
    {
        if (! in_array($this->extrema, ['min', 'max'], true)) {
            throw new InvalidArgumentException('Invalid extrema');
        }
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join_orders = Join::left("{$wpdb->prefix}wc_orders", 'acsort_orders')
            ->on('acsort_orders.customer_id', '=', "$wpdb->users.ID");

        if ($this->status) {
            $placeholders = implode(', ', array_fill(0, count($this->status), '%s'));
            $join_orders->on('acsort_orders.status', 'IN', '(' . $wpdb->prepare($placeholders, $this->status) . ')');
        }

        $bindings->group_by("$wpdb->users.ID");
        $bindings->join(
            $join_orders->get_sql()
            . "\n" .
            Join::left("{$wpdb->prefix}wc_order_operational_data", $alias)
                ->on("$alias.order_id", '=', 'acsort_orders.id')
                ->get_sql()
        );

        $computation_type = 'min' === $this->extrema
            ? ComputationType::MIN
            : ComputationType::MAX;

        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType($computation_type),
                "$alias.date_completed_gmt",
                (string)$order
            )
        );
    }

}
