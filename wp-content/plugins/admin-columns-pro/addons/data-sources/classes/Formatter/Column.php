<?php

declare(strict_types=1);

namespace ACA\DataSources\Formatter;

use AC\Exception\ValueNotFoundException;
use AC\Setting\Formatter;
use AC\Type\Value;
use AC\Type\ValueCollection;

class Column implements Formatter
{
    private string $column;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function format(Value $value)
    {
        $has_value = $value->get_value()[$this->column] ?? false;

        if ($has_value === false) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        $column_value = $value->get_value()[$this->column];

        if ($column_value instanceof ValueCollection) {
            return $column_value;
        }

        return $value->with_value($column_value);
    }

}
