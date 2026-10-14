<?php

declare(strict_types=1);

namespace ACA\DataSources\Type;

final class Cardinality
{
    private const HAS_ONE = '1:1';
    private const HAS_MANY = '1:M';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * There is a one on one match
     */
    public static function has_one(): self
    {
        return new self(self::HAS_ONE);
    }

    /**
     * There is a one to many match
     */
    public static function has_many(): self
    {
        return new self(self::HAS_MANY);
    }

    public function is_to_one(): bool
    {
        return $this->value === self::HAS_ONE;
    }

    public function is_to_many(): bool
    {
        return $this->value === self::HAS_MANY;
    }

    public function __toString(): string
    {
        return $this->value;
    }

}
