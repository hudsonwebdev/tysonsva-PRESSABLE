<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Formatter;

use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;

class DaysFormatter extends NumberFormatter
{
    protected function format_value(string $value): string
    {
        $formatted = parent::format_value($value);

        if (in_array($this->aggregation_type->get_value(), [
            AggregationType::SUM,
            AggregationType::AVG,
            AggregationType::MIN,
            AggregationType::MAX,
            AggregationType::RANGE,
        ], true)) {
            return sprintf(
                '%s %s',
                $formatted,
                _n('day', 'days', (int)round((float)$value), 'codepress-admin-columns')
            );
        }

        return $formatted;
    }

}
