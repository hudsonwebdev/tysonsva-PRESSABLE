<?php

declare(strict_types=1);

namespace ACP\Metric\Formatter;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;

abstract class BaseFormatter implements Metric\Formatter
{
    protected AggregationType $aggregation_type;

    public function __construct(AggregationType $aggregation_type)
    {
        $this->aggregation_type = $aggregation_type;
    }

    final public function format(string $value): string
    {
        if ($this->aggregation_type->is_percentage()) {
            return round((float)$value, 2) . '%';
        }

        return $this->format_value($value);
    }

    abstract protected function format_value(string $value): string;

}
