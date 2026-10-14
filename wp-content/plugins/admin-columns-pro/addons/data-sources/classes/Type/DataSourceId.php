<?php

declare(strict_types=1);

namespace ACA\DataSources\Type;

use AC\Type\TableId;
use ACA\DataSources\Addon;
use ACA\DataSources\Exception\InvalidIdentifierException;

final class DataSourceId
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;

        if (! preg_match('/^[a-z][a-z0-9]*([_a-z0-9]+)*$/', $id)) {
            throw new InvalidIdentifierException();
        }
    }

    public function to_table_screen_id(): TableId
    {
        return new TableId(Addon::slug((string)$this));
    }

    public function equals(self $data_source_id): bool
    {
        return (string)$this === (string)$data_source_id;
    }

    public function value(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

}
