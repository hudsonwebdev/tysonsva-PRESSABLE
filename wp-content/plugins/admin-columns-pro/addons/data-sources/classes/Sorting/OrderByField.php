<?php

declare(strict_types=1);

namespace ACA\DataSources\Sorting;

use ACP\Query\Bindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class OrderByField extends QueryBindings
{
    private array $fields;

    public function __construct(
        string $field,
        array $fields,
        ?DataType $data_type = null,
        array $empty_values = []
    ) {
        parent::__construct($field, $data_type, $empty_values);
        $this->fields = $fields;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $sql = SqlOrderByFactory::create_with_field($this->field, $this->fields, (string)$order);

        $bindings->order_by($sql);

        return $bindings;
    }

}
