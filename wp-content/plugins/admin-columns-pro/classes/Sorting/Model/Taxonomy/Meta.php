<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Meta extends Base
{
    protected string $meta_key;

    protected DataType $data_type;

    public function __construct(string $meta_key, ?DataType $data_type = null)
    {
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->termmeta, $alias)
                ->on('t.term_id', '=', "$alias.term_id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
        );

        $bindings->group_by("t.term_id");
        $bindings->order_by($this->get_order_by($alias, $order));
    }

    protected function get_order_by(string $alias, Order $order): string
    {
        return SqlOrderByFactory::create(
            "$alias.`meta_value`",
            (string)$order,
            [
                'cast_type' => (string)CastType::create_from_data_type($this->data_type),
            ]
        );
    }

}
