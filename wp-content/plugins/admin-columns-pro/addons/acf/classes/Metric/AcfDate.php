<?php

declare(strict_types=1);

namespace ACA\ACF\Metric;

use ACP\Metric\Config\Meta\Date;

class AcfDate extends Date
{
    protected function get_column_expression(string $alias): string
    {
        return sprintf("CAST(STR_TO_DATE(NULLIF(%s.meta_value, ''), '%%Y%%m%%d') AS DATETIME)", $alias);
    }

}
