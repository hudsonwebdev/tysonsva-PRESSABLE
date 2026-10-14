<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Query\Bindings;

interface Aggregation
{
    public function apply(Bindings $bindings, string $column): void;

}
