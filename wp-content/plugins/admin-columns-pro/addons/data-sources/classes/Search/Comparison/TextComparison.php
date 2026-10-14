<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use ACA\DataSources;
use ACP\Search\Operators;

class TextComparison extends DataSources\Search\Comparison
{
    public function __construct(string $id, ?Operators $operators = null)
    {
        if (! $operators) {
            $operators = new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
                Operators::EQ,
                Operators::NEQ,
                Operators::BEGINS_WITH,
                Operators::ENDS_WITH,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]);
        }

        parent::__construct($id, $operators);
    }

}
