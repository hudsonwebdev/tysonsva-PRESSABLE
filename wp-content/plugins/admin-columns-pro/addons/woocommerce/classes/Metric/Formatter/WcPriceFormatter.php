<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Formatter;

use ACP\Metric\Formatter\NumberFormatter;

class WcPriceFormatter extends NumberFormatter
{
    protected function number_formatter(float $number): string
    {
        return strip_tags(wc_price($number));
    }

}
