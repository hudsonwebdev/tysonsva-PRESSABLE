<?php

declare(strict_types=1);

namespace ACP\Search;

use LogicException;

final class Value
{
    public const INT = 'int';
    public const DECIMAL = 'decimal';
    public const STRING = 'string';
    public const DATE = 'date';

    protected string $type;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value, ?string $type = null)
    {
        $this->value = $value;
        $this->type = $type ?? self::STRING;

        $this->validate_type();
    }

    private function validate_type(): void
    {
        $types = [self::INT, self::DECIMAL, self::STRING, self::DATE];

        if (! in_array($this->type, $types, true)) {
            throw new LogicException('Invalid type found.');
        }
    }

    public function with_value($value): self
    {
        return new self($value, $this->type);
    }

    public function get_type(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
    }

}
