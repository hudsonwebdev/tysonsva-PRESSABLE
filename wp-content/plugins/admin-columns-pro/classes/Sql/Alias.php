<?php

declare(strict_types=1);

namespace ACP\Sql;

use InvalidArgumentException;

final class Alias
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;

        $this->validate();
    }

    public static function generate(string $prefix = ''): self
    {
        return new self(AliasGenerator::generate($prefix));
    }

    private function validate(): void
    {
        if (! preg_match('/^[a-z][a-z0-9]*(_[a-z0-9]+)*$/', $this->alias)) {
            throw new InvalidArgumentException(
                'Characters allowed: [a-z], [0-9] and underscores and should start with a letter.'
            );
        }
    }

    public function __toString(): string
    {
        return $this->alias;
    }

}
