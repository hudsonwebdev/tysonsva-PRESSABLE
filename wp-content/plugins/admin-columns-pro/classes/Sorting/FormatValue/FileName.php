<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class FileName implements FormatValue
{
    public function format_value($value): string
    {
        if (! is_string($value) || ! $value) {
            return '';
        }

        return strtolower(basename($value));
    }

}
