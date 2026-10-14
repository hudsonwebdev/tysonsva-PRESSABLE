<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use ACA\DataSources;
use ACP\Search\Operators;
use ACP\Search\Value;

class NumberComparison extends DataSources\Search\Comparison
{
    public function __construct(string $id, ?Operators $operators = null)
    {
        if (! $operators) {
            $operators = new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::LT,
                Operators::GT,
                Operators::BETWEEN,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]);
        }

        parent::__construct($id, $operators, Value::INT);
    }

}
