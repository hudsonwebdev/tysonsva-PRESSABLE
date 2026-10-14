<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\Config;

use ACA\DataSources\DataSource\ColumnLabelResolver;
use DomainException;

final class Columns
{
    private ?Identifier $identifier;

    /**
     * @param Column[] $columns
     */
    private array $columns = [];

    private ?ColumnLabelResolver $column_label_resolver;

    public function __construct(
        ?Identifier $identifier = null,
        array $columns = [],
        ?ColumnLabelResolver $column_label_resolver = null
    ) {
        $this->identifier = $identifier;
        $this->column_label_resolver = $column_label_resolver;

        foreach ($columns as $column) {
            $this->add_column($column);
        }
    }

    public static function create(): self
    {
        return new self();
    }

    public function with_identifier(Identifier $identifier): self
    {
        $clone = clone $this;
        $clone->identifier = $identifier;

        return $clone;
    }

    public function with_label_resolver(ColumnLabelResolver $column_label_resolver): self
    {
        $clone = clone $this;
        $clone->column_label_resolver = $column_label_resolver;

        return $clone;
    }

    private function add_column(Column $column): void
    {
        $this->columns[$column->get_name()] = $column;
    }

    public function with_column(Column $column): self
    {
        $clone = clone $this;
        $clone->add_column($column);

        return $clone;
    }

    public function with_columns(array $columns): self
    {
        $clone = clone $this;
        $clone->columns = [];

        foreach ($columns as $column) {
            $clone->add_column($column);
        }

        return $clone;
    }

    public function has_identifier(): bool
    {
        return $this->identifier !== null;
    }

    public function get_identifier(): Identifier
    {
        if (! $this->has_identifier()) {
            throw new DomainException('No identifier supplied.');
        }

        return $this->identifier;
    }

    public function has_column(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    public function get_column(string $name): Column
    {
        if (! $this->has_column($name)) {
            throw new DomainException(
                sprintf('Column with name `%s` does not exist.', $name)
            );
        }

        return $this->columns[$name];
    }

    public function has_column_label_resolver(): bool
    {
        return $this->column_label_resolver !== null;
    }

    public function get_column_label_resolver(): ColumnLabelResolver
    {
        if (! $this->has_column_label_resolver()) {
            throw new DomainException('No ColumnLabelResolver supplied');
        }

        return $this->column_label_resolver;
    }

}
