<?php

declare(strict_types=1);

namespace ACP\Sql\Expression\Conditional;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class Coalesce implements Expression
{
    private string $expression;

    private string $fallback;

    /**
     * @param string|Expression $expression
     */
    public function __construct($expression, string $fallback)
    {
        if (! is_string($expression) && ! $expression instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($expression);
        }

        $this->expression = (string)$expression;
        $this->fallback = $fallback;
    }

    public function __toString(): string
    {
        return sprintf('COALESCE(%s, %s)', $this->expression, $this->fallback);
    }

}
