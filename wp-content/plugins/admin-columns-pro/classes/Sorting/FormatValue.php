<?php

declare(strict_types=1);

namespace ACP\Sorting;

interface FormatValue
{
    /**
     * @param mixed $value
     *
     * @return string|int|float|bool|null
     */
    public function format_value($value);

}
