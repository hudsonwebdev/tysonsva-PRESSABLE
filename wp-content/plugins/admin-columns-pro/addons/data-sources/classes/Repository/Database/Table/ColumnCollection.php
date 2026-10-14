<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table;

use AC\Collection;
use DomainException;

final class ColumnCollection extends Collection
{
    public function __construct(array $columns)
    {
        foreach ($columns as $column) {
            $this->add($column);
        }
    }

    private function add(Column $column): void
    {
        $this->data[$column->get_name()] = $column;
    }

    public function has(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function get(string $name): Column
    {
        if (! $this->has($name)) {
            throw new DomainException(sprintf('Column `%s` not found.', $name));
        }

        return $this->data[$name];
    }

    public function current(): Column
    {
        return current($this->data);
    }

}
