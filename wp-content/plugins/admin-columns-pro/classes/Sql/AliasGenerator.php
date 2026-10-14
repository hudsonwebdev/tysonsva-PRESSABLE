<?php

declare(strict_types=1);

namespace ACP\Sql;

final class AliasGenerator
{
    private static int $count = 1;

    /**
     * Generates a (unique) alias for SQL queries.
     *
     * Use a prefix to easily identify the alias
     */
    public static function generate(string $prefix = ''): string
    {
        if ($prefix) {
            $prefix = rtrim($prefix, '_') . '_';
        }

        return $prefix . 'ac' . self::$count++;
    }

    /**
     * Generates a prefix from a fully qualified class name.
     *
     * For example, Namespace\ClassName becomes namespace_classname.
     *
     * @see self::generate()
     */
    public static function generate_from_class(string $class): string
    {
        $prefix = strtolower(str_replace('\\', '_', $class));

        return self::generate($prefix);
    }

}
