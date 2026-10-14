<?php

declare(strict_types=1);

namespace ACA\DataSources\Search;

use ACP\Query\Bindings;
use ACP\Search\Value;

interface QueryBindingsFactory
{
    public function create(Comparison $comparison, string $operator, Value $value): Bindings;

}
