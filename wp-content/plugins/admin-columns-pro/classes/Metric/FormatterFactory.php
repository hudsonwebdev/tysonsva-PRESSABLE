<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Metric\Type\AggregationType;

interface FormatterFactory
{
    public function create(AggregationType $aggregation_type): Formatter;

}
