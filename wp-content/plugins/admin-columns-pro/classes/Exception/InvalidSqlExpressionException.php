<?php

declare(strict_types=1);

namespace ACP\Exception;

use InvalidArgumentException;

final class InvalidSqlExpressionException extends InvalidArgumentException
{
    public static function from_value($value): self
    {
        return new self(
            sprintf('Expected string or Expression, got %s', get_debug_type($value))
        );
    }

}
