<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table;

/**
 * Partial representation of a MySQL column from the information schema
 */
final class Column
{
    private string $name;

    private string $data_type;

    private string $key;

    public function __construct(string $name, string $data_type, string $key)
    {
        $this->name = $name;
        $this->data_type = $data_type;
        $this->key = $key;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_data_type(): string
    {
        return $this->data_type;
    }

    public function is_primary_key(): bool
    {
        return $this->key === 'PRI';
    }

    public function __toString(): string
    {
        return $this->name;
    }

}
