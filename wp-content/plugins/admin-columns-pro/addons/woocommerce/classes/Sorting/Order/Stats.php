<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Stats extends Base
{
    private string $field;

    private ?DataType $data_type;

    public function __construct(string $field, ?DataType $data_type = null)
    {
        $this->field = $field;
        $this->data_type = $data_type;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_stats = $wpdb->prefix . 'wc_order_stats';

        $bindings->join(
            Join::left($table_stats, $alias)
                ->on("$alias.order_id", '=', "$table_orders.id")
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
            case DataType::DATE:
                return [EmptyValues::NULL, EmptyValues::ZERO];
            case DataType::NUMERIC:
                return [EmptyValues::NULL, EmptyValues::LTE_ZERO];
            default:
                return [EmptyValues::NULL, EmptyValues::EMPTY_STRING];
        }
    }

}
