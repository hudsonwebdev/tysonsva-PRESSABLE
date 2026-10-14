<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class OrderMeta extends Base
{
    private string $meta_field;

    private DataType $data_type;

    public function __construct(string $meta_field, ?DataType $data_type = null)
    {
        $this->meta_field = $meta_field;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        if (DataType::DATETIME === $this->data_type->get_value()) {
            $order = new Order('ASC' === (string)$order ? 'DESC' : 'ASC');
        }

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_addresses = $wpdb->prefix . 'wc_orders_meta';

        $bindings->join(
            Join::left($table_addresses, $alias)
                ->on("$alias.order_id", '=', "$table_orders.id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_field))
                ->get_sql()
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.meta_value",
                (string)$order,
                [
                    'cast_type' => CastType::create_from_data_type($this->data_type)->get_value(),
                ]
            )
        );
    }

}
