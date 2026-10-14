<?php

declare(strict_types=1);

namespace ACP\Sql;

final class Identifier
{
    private const QUOTE = '`';

    /**
     * Add quotes (backticks) to ensure valid identifiers
     *
     * Caveats:
     * - Does not support expression such as * or COUNT(*)
     * - A `.` (dot) is treated as an identifier qualifier.
     *   Use `self::quote_alias()` for a single identifier that contains a literal dot.
     */
    public static function quote(string $identifier): string
    {
        $parts = [];

        foreach (explode('.', $identifier) as $part) {
            $parts[] = self::quote_part($part);
        }

        return implode('.', $parts);
    }

    // TODO consider string|\Stringable in the future
    public static function quote_qualified(string $qualifier, string $identifier): string
    {
        return self::quote_part($qualifier) . '.' . self::quote_part($identifier);
    }

    public static function quote_alias(string $identifier, ?string $alias = null): string
    {
        $identifier = self::quote($identifier);

        if ($alias) {
            $identifier .= ' AS ' . self::quote_part($alias);
        }

        return $identifier;
    }

    private static function quote_part(string $part): string
    {
        $part = trim($part);
        $part = trim($part, self::QUOTE);

        return self::QUOTE . $part . self::QUOTE;
    }

}
