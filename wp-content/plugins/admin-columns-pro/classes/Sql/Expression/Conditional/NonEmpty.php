<?php

declare(strict_types=1);

namespace ACP\Sql\Expression\Conditional;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class NonEmpty implements Expression
{
    private string $expression;

    /**
     * @param string|Expression $expression
     */
    public function __construct($expression)
    {
        if (! is_string($expression) && ! $expression instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($expression);
        }

        $this->expression = (string)$expression;
    }

    public function __toString(): string
    {
        return sprintf(
            "(%s IS NOT NULL AND %s NOT IN ('', '0')) + 0",
            $this->expression,
            $this->expression
        );
    }

}
