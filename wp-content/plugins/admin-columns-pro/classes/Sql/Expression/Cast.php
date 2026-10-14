<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Exception\InvalidSqlExpressionException;
use ACP\Sql\Expression;

final class Cast implements Expression
{
    private string $expression;

    private string $type;

    /**
     * @param string|Expression $expression
     */
    public function __construct($expression, string $type)
    {
        if (! is_string($expression) && ! $expression instanceof Expression) {
            throw InvalidSqlExpressionException::from_value($expression);
        }

        $this->expression = (string)$expression;
        $this->type = $type;
    }

    public function __toString(): string
    {
        return sprintf('CAST(%s AS %s)', $this->expression, $this->type);
    }

}
