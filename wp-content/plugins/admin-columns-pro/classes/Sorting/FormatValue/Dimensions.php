<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Dimensions implements FormatValue
{
    public function format_value($value)
    {
        $data = maybe_unserialize($value);

        return isset($data['width'], $data['height'])
            ? ((int)$data['width']) * ((int)$data['height'])
            : null;
    }

}
