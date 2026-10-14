<?php

declare(strict_types=1);

namespace ACP\Metric\Aggregation;

trait Percentage
{
    /**
     * Wrap a boolean condition as a percentage (of all rows) expression.
     */
    protected function percentage_of(string $condition): string
    {
        return sprintf('ROUND(SUM(CASE WHEN %s THEN 1 ELSE 0 END) / COUNT(*) * 100, 2)', $condition);
    }

}
