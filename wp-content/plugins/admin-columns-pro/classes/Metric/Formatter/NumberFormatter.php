<?php

declare(strict_types=1);

namespace ACP\Metric\Formatter;

use ACP\Metric\Type\AggregationType;

class NumberFormatter extends BaseFormatter
{
    private int $max_decimals;

    public function __construct(AggregationType $aggregation_type, int $max_decimals = 2)
    {
        parent::__construct($aggregation_type);

        $this->max_decimals = $max_decimals;
    }

    protected function number_formatter(float $number): string
    {
        for ($decimals = 0; $decimals <= $this->max_decimals; $decimals++) {
            if (round($number, $decimals) === $number) {
                return number_format_i18n($number, $decimals);
            }
        }

        return number_format_i18n($number, $this->max_decimals);
    }

    protected function format_value(string $value): string
    {
        switch ($this->aggregation_type->get_value()) {
            case AggregationType::SUM:
            case AggregationType::AVG:
            case AggregationType::MIN:
            case AggregationType::MAX:
            case AggregationType::RANGE:
            case AggregationType::IS_EMPTY:
            case AggregationType::IS_NOT_EMPTY:
            case AggregationType::COUNT:
                return $this->number_formatter((float)$value);
            default:
                return (string)round((float)$value, $this->max_decimals);
        }
    }

}
