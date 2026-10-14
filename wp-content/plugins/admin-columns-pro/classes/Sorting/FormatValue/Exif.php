<?php

declare(strict_types=1);

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Exif implements FormatValue
{
    private string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function format_value($value)
    {
        $data = maybe_unserialize($value);

        return $data['image_meta'][$this->field] ?? null;
    }

}
