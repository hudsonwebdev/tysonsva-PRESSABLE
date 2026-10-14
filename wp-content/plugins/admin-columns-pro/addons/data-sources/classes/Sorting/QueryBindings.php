<?php

declare(strict_types=1);

namespace ACA\DataSources\Sorting;

use ACP\Query\Bindings;
use ACP\Sorting;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;

class QueryBindings implements Sorting\Model\QueryBindings
{
    protected string $field;

    protected DataType $data_type;

    protected array $empty_values;

    public function __construct(string $field, ?DataType $data_type = null, array $empty_values = [])
    {
        $this->field = $field;
        $this->data_type = $data_type ?? new DataType(DataType::STRING);

        if (! count($empty_values)) {
            $empty_values = [EmptyValues::NULL];

            if (DataType::STRING === (string)$this->data_type) {
                $empty_values[] = EmptyValues::EMPTY_STRING;
            }
        }

        $this->empty_values = $empty_values;
    }

    public function with_field(string $field): self
    {
        $clone = clone $this;
        $clone->field = $field;

        return $clone;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $bindings->order_by(
            SqlOrderByFactory::create(
                $this->field,
                (string)$order,
                [
                    'empty_values' => $this->empty_values,
                ]
            )
        );

        return $bindings;
    }

}
