<?php

declare(strict_types=1);

namespace ACP\Sorting\Type;

use InvalidArgumentException;

final class Order
{
    private const ASC = 'ASC';
    private const DESC = 'DESC';

    private string $order;

    public function __construct(string $order)
    {
        $this->order = $order;

        $this->validate();
    }

    public static function ascending(): self
    {
        return new self(self::ASC);
    }

    public static function descending(): self
    {
        return new self(self::DESC);
    }

    public static function create_by_string(string $order): self
    {
        $order = strtoupper($order);

        if (self::ASC !== $order) {
            $order = self::DESC;
        }

        return new self($order);
    }

    private function validate(): void
    {
        $valid = [self::ASC, self::DESC];

        if (! in_array($this->order, $valid, true)) {
            throw new InvalidArgumentException(vsprintf('Expected %s or %s.', $valid));
        }
    }

    public function __toString(): string
    {
        return $this->order;
    }

}
