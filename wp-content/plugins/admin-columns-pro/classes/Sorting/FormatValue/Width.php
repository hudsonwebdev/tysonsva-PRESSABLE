<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Width implements FormatValue
{
    public function format_value($value)
    {
        $data = maybe_unserialize($value);

        return $data['width'] ?? null;
    }

}
