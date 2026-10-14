<?php

declare(strict_types=1);

namespace ACP\Metric\Formatter;

use ACP\Metric\Type\AggregationType;
use DateTime;

class DateFormatter extends BaseFormatter
{
    private string $input_format;

    private string $output_format;

    public function __construct(
        AggregationType $aggregation_type,
        string $input_format = 'Y-m-d H:i:s',
        ?string $output_format = null
    ) {
        parent::__construct($aggregation_type);

        $this->input_format = $input_format;
        $this->output_format = $output_format ?? (string)get_option('date_format', 'Y-m-d');
    }

    protected function format_value(string $value): string
    {
        switch ($this->aggregation_type->get_value()) {
            case AggregationType::DATE_MIN:
            case AggregationType::DATE_MAX:
                return $this->format_date($value);
            case AggregationType::DATE_RANGE:
                return $this->readable_date_range((int)$value);
            default:
                return $value;
        }
    }

    private function format_date(string $value): string
    {
        $date = DateTime::createFromFormat($this->input_format, $value);

        if (! $date) {
            return __('Invalid date', 'codepress-admin-columns');
        }

        return $date->format($this->output_format);
    }

    private function readable_date_range(int $days): string
    {
        if ($days < 0) {
            $days = 0;
        }

        $days_in_month = 31;
        $days_in_year = 365;

        if ($days < $days_in_month) {
            return sprintf(_n('%d day', '%d days', $days, 'codepress-admin-columns'), $days);
        }

        if ($days < $days_in_year) {
            $months = round($days / $days_in_month, 1);

            return sprintf(_n('%s month', '%s months', (int)ceil($months), 'codepress-admin-columns'), $months);
        }

        $years = round($days / $days_in_year, 1);

        return sprintf(_n('%s year', '%s years', (int)ceil($years), 'codepress-admin-columns'), $years);
    }

}
