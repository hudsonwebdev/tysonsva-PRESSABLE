<?php

declare(strict_types=1);

namespace ACP\Metric\Aggregation;

use ACP\Metric\Aggregation;
use ACP\Query\Bindings;

class Range implements Aggregation
{
    public function apply(Bindings $bindings, string $column): void
    {
        $bindings->fields(sprintf('MAX(%s)-MIN(%s)', $column, $column));
    }
}
