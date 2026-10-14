<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\Config;

use ACA\DataSources\DataSource\ColumnType;

final class Column
{
    /**
     * Must match the column name from the database table
     */
    private string $name;

    private ?string $label = null;

    private ?ColumnType $type;

    /**
     * This tells the settings page if this column is part of the default columns.
     */
    private ?bool $default_column;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function for(string $name): self
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

    public function has_label(): bool
    {
        return $this->label !== null;
    }

    public function get_label(): ?string
    {
        return $this->label;
    }

    public function has_type(): bool
    {
        return $this->type !== null;
    }

    public function get_type(): ?ColumnType
    {
        return $this->type;
    }

    public function is_default_column(): ?bool
    {
        return $this->default_column;
    }

}
