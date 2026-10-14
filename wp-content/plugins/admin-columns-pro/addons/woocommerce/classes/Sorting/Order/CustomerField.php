<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class CustomerField extends Base
{
    private string $field;

    protected DataType $data_type;

    public function __construct(string $field, ?DataType $data_type = null)
    {
        $this->field = $field;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_customer = $wpdb->prefix . 'wc_customer_lookup';

        $bindings->join(
            Join::left($table_customer, $alias)
                ->on("$alias.user_id", '=', "$table_orders.customer_id")
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.$this->field",
                (string)$order,
                [
                    'empty_values' => $this->get_empty_values(),
                ]
            )
        );
    }

    protected function get_empty_values(): array
    {
        switch ($this->data_type) {
            case DataType::NUMERIC:
                return [null, 0];
            default:
                return [null, ''];
        }
    }

}
