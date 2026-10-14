<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACA\WC\Type\AddressType;
use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;
use InvalidArgumentException;

class AddressField extends Base
{
    private string $address_field;

    private AddressType $address_type;

    public function __construct(string $address_field, AddressType $address_type)
    {
        $this->address_field = $address_field;
        $this->address_type = $address_type;

        $this->validate();
    }

    private function validate(): void
    {
        if ('' === $this->address_field) {
            throw new InvalidArgumentException('Invalid address field');
        }
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_addresses = $wpdb->prefix . 'wc_order_addresses';

        $bindings->join(
            Join::left($table_addresses, $alias)
                ->on("$alias.order_id", '=', "$table_orders.id")
                ->on("$alias.address_type", '=', $wpdb->prepare('%s', (string)$this->address_type))
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.$this->address_field",
                (string)$order
            )
        );
    }

}
