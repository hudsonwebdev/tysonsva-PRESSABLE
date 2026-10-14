<?php

declare(strict_types=1);

namespace ACP\Metric;

interface Formatter
{
    /**
     * Format a metric value based on aggregation type
     */
    public function format(string $value): string;

}
