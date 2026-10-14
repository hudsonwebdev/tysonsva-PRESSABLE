<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class TimestampDiff implements Expression
{
    public const SECOND = 'SECOND';
    public const MINUTE = 'MINUTE';
    public const HOUR = 'HOUR';
    public const DAY = 'DAY';
    public const WEEK = 'WEEK';
    public const MONTH = 'MONTH';
    public const YEAR = 'YEAR';

    private string $unit;

    private string $date1;

    private string $date2;

    /**
     * @param string|Expression $date1
     * @param string|Expression $date2
     */
    public function __construct(string $unit, $date1, $date2)
    {
        if (! is_string($date1) && ! $date1 instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($date1);
        }

        if (! is_string($date2) && ! $date2 instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($date2);
        }

        $this->unit = $unit;
        $this->date1 = (string)$date1;
        $this->date2 = (string)$date2;
    }

    public function __toString(): string
    {
        return sprintf('TIMESTAMPDIFF(%s, %s, %s)', $this->unit, $this->date1, $this->date2);
    }

}
