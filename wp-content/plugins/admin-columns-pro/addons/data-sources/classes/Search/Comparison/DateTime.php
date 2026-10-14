<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use ACA\DataSources;
use ACP\Query\Bindings;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class DateTime extends DataSources\Search\Comparison
{
    public function __construct(string $id, ?Operators $operators = null)
    {
        if (! $operators) {
            $operators = new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::TODAY,
                Operators::PAST,
                Operators::FUTURE,
                Operators::LT,
                Operators::GT,
                Operators::BETWEEN,
                Operators::WITHIN_DAYS,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]);
        }

        parent::__construct($id, $operators, Value::DATE, new Labels\Date());
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        switch ($operator) {
            case Operators::WITHIN_DAYS:
                $value = new Value($value->get_value(), Value::INT);
                break;
            case Operators::EQ:
                $operator = Operators::CONTAINS;
                break;
            case Operators::NEQ:
                $operator = Operators::NOT_CONTAINS;
                break;
        }

        return parent::create_query_bindings($operator, $value);
    }

}
