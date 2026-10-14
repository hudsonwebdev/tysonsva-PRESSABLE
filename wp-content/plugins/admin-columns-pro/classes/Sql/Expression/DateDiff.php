<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class DateDiff implements Expression
{
    private string $date1;

    private string $date2;

    /**
     * @param string|Expression $date1
     * @param string|Expression $date2
     */
    public function __construct($date1, $date2)
    {
        if (! is_string($date1) && ! $date1 instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($date1);
        }

        if (! is_string($date2) && ! $date2 instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($date2);
        }

        $this->date1 = (string)$date1;
        $this->date2 = (string)$date2;
    }

    public function __toString(): string
    {
        return sprintf('DATEDIFF(%s, %s)', $this->date1, $this->date2);
    }

}
