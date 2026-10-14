<?php

declare(strict_types=1);

namespace ACA\DataSources\Type;

use InvalidArgumentException;

final class ColumnName
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function from(string $name): self
    {
        $name = trim($name);

        if (! preg_match('/^[A-Za-z_]\w*$/', $name)) {
            throw new InvalidArgumentException('Invalid column name.');
        }

        return new self($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }

}
