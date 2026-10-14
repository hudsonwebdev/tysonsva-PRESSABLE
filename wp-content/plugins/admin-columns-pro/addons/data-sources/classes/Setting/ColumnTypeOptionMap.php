<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting;

use AC\Setting\Control\OptionCollection;
use AC\Setting\Control\Type\Option;

final class ColumnTypeOptionMap
{
    private array $map = [];

    private array $index = [];

    public function add(string $column_type, Option $option): void
    {
        $this->map[$column_type] = $option;
        $this->index[$option->get_value()] = $column_type;
    }

    public function get_option(string $column_type): ?Option
    {
        return $this->map[$column_type] ?? null;
    }

    public function get_options(): OptionCollection
    {
        return new OptionCollection(array_values($this->map));
    }

    public function get_column_type(string $value): ?string
    {
        return $this->index[$value] ?? null;
    }

}
