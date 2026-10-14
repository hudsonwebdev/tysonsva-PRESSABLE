<?php

declare(strict_types=1);

namespace ACA\DataSources;

use DomainException;

final class ColumnFactoryMap
{
    private array $map = [];

    public function add(string $column_reference, string $factory): void
    {
        $this->map[$column_reference] = $factory;
    }

    public function has(string $column_reference): bool
    {
        return isset($this->map[$column_reference]);
    }

    public function get(string $column_reference): string
    {
        if (! $this->has($column_reference)) {
            throw new DomainException(
                sprintf('No factory registered for `%s`.', $column_reference)
            );
        }

        return $this->map[$column_reference];
    }

}
