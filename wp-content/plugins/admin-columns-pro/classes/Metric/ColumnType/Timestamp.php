<?php

declare(strict_types=1);

namespace ACP\Metric\ColumnType;

class Timestamp extends Date
{
    public function get_column_expression(string $column): string
    {
        return parent::get_column_expression(sprintf('FROM_UNIXTIME(%s)', $column));
    }

}
