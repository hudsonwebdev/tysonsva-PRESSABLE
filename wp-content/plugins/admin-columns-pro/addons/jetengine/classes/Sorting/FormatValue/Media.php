<?php

declare(strict_types=1);

namespace ACA\JetEngine\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Media implements FormatValue
{
    public function format_value($value)
    {
        if (! is_numeric($value)) {
            return '';
        }

        $file = get_attached_file((int)$value);

        return $file
            ? basename($file)
            : '';
    }

}
