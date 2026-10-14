<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database;

use ACA\DataSources\Repository\Database\Table\Column;
use ACA\DataSources\Repository\Database\Table\ColumnCollection;
use ACA\DataSources\Repository\Database\Table\Filter;
use DomainException;

final class Table
{
    private string $name;

    private string $identifier;

    private ColumnCollection $columns;

    public function __construct(string $name, string $identifier, ColumnCollection $columns)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->columns = $columns;

        $this->validate();
    }

    private function validate(): void
    {
        if (! $this->columns->has($this->identifier)) {
            throw new DomainException(
                sprintf(
                    'Identifier `%s` must be part of the provided columns.',
                    $this->identifier
                )
            );
        }
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_identifier(): Column
    {
        return $this->columns->get($this->identifier);
    }

    public function get_columns(): ColumnCollection
    {
        return $this->columns;
    }

    /**
     * Filter out certain columns based on the Filter interface.
     *
     * Returns a new Table with updated columns.
     */
    public function filter(Filter $filter): self
    {
        $filtered = [];

        foreach ($this->columns as $column) {
            if ($filter->matches($column)) {
                $filtered[] = $column;
            }
        }

        return new self(
            $this->name,
            $this->identifier,
            new ColumnCollection($filtered)
        );
    }

}
