<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Formatter;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;

class WcPriceFormatterFactory implements Metric\FormatterFactory
{
    public function create(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }
}
