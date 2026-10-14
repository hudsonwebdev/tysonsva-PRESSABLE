<?php

declare(strict_types=1);

namespace ACP\Sql;

final class JoinType
{
    private const INNER = 'INNER';
    private const LEFT = 'LEFT';

    private string $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Required match (INNER JOIN)
     */
    public static function inner(): self
    {
        return new self(self::INNER);
    }

    /**
     * Optional match (LEFT JOIN)
     */
    public static function left(): self
    {
        return new self(self::LEFT);
    }

    public function __toString(): string
    {
        return $this->type;
    }

}
