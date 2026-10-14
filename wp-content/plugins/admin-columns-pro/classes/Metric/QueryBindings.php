<?php

declare(strict_types=1);

namespace ACP\Metric;

use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;

interface QueryBindings
{
    public function create(AggregationType $aggregation_type): Bindings;

}
