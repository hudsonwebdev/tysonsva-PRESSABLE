<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class Round implements Expression
{
    private string $expression;

    private int $decimals;

    /**
     * @param string|Expression $expression
     */
    public function __construct($expression, int $decimals = 0)
    {
        if (! is_string($expression) && ! $expression instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($expression);
        }

        $this->expression = (string)$expression;
        $this->decimals = $decimals;
    }

    public function __toString(): string
    {
        if ($this->decimals === 0) {
            return sprintf('ROUND(%s)', $this->expression);
        }

        return sprintf('ROUND(%s, %d)', $this->expression, $this->decimals);
    }

}
