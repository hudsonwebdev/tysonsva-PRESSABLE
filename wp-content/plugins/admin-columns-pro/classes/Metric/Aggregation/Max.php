<?php

declare(strict_types=1);

namespace ACP\Metric\Aggregation;

use ACP\Metric\Aggregation;
use ACP\Query\Bindings;

class Max implements Aggregation
{
    public function apply(Bindings $bindings, string $column): void
    {
        $bindings->fields(sprintf('MAX(%s)', $column));
    }

}
