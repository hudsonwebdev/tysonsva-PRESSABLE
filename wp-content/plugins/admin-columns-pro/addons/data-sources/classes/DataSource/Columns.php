<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use AC\Collection;
use ACA\DataSources\DataSource\ColumnType\IdentifierType;
use DomainException;

final class Columns extends Collection
{
    private string $identifier;

    public function __construct(
        string $identifier,
        array $columns
    ) {
        $this->identifier = $identifier;

        foreach ($columns as $column) {
            $this->add($column);
        }

        $this->validate();
    }

    private function validate(): void
    {
        if (! $this->has($this->identifier)) {
            throw new DomainException(
                sprintf(
                    'Identifier `%s` must be part of the provided columns.',
                    $this->identifier
                )
            );
        }

        $identifier = $this->get($this->identifier);

        if (! $identifier->get_type() instanceof IdentifierType) {
            throw new DomainException(sprintf('Identifier should have %s as type.', IdentifierType::class));
        }

        if (! $identifier->is_default_column()) {
            throw new DomainException('Identifier must be a default column.');
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

    public function get_identifier(): Column
    {
        return $this->get($this->identifier);
    }

    public function current(): Column
    {
        return current($this->data);
    }

}
