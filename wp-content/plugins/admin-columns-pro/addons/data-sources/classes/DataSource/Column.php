<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use ACA\DataSources\DataSource\ColumnType\ConfigurableType;

final class Column
{
    /**
     * Must match the column name from the database table
     */
    private string $name;

    private string $label;

    private ColumnType $type;

    /**
     * This tells the settings page whether this column is part of the default columns.
     */
    private bool $default_column;

    public function __construct(
        string $name,
        ?string $label = null,
        ?ColumnType $type = null,
        ?bool $default_column = null
    ) {
        $this->name = $name;
        $this->label = $label ?? $name;
        $this->type = $type ?? new ConfigurableType();
        $this->default_column = $default_column ?? true;
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function with_type(ColumnType $type): self
    {
        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    public function with_label(string $label): self
    {
        $clone = clone $this;
        $clone->label = $label;

        return $clone;
    }

    public function as_non_default_column(): self
    {
        $clone = clone $this;
        $clone->default_column = false;

        return $clone;
    }

    public function as_default_column(): self
    {
        $clone = clone $this;
        $clone->default_column = true;

        return $clone;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_label(): string
    {
        return $this->label;
    }

    public function get_type(): ColumnType
    {
        return $this->type;
    }

    public function is_default_column(): bool
    {
        return $this->default_column;
    }

    public function equals(self $column): bool
    {
        return $column->get_name() === $this->get_name();
    }

}
