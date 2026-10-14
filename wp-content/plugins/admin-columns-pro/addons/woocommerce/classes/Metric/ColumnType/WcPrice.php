<?php

declare(strict_types=1);

namespace ACA\WC\Metric\ColumnType;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\Type\AggregationType;

class WcPrice extends Metric\ColumnType\Numeric
{
    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

}
