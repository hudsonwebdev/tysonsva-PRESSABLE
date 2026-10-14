<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class Greatest implements Expression
{
    private string $expression;

    private string $value;

    /**
     * @param string|Expression $expression
     */
    public function __construct($expression, string $value)
    {
        if (! is_string($expression) && ! $expression instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($expression);
        }

        $this->expression = (string)$expression;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return sprintf('GREATEST(%s, %s)', $this->expression, $this->value);
    }

}
