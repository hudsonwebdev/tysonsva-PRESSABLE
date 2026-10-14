<?php

declare(strict_types=1);

namespace ACP\Metric\Aggregation;

use ACP\Metric\Aggregation;
use ACP\Query\Bindings;

class IsEmpty implements Aggregation
{
    use Percentage;

    private bool $as_percentage;

    public function __construct(bool $as_percentage = false)
    {
        $this->as_percentage = $as_percentage;
    }

    public function apply(Bindings $bindings, string $column): void
    {
        $condition = sprintf('%1$s IS NULL OR %1$s = ""', $column);

        if ($this->as_percentage) {
            $bindings->fields($this->percentage_of($condition));

            return;
        }

        $bindings->fields('COUNT(*)');
        $bindings->where(sprintf('(%s)', $condition));
    }

}
