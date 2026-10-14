<?php

declare(strict_types=1);

namespace ACA\DataSources;

/**
 * Slug and key namespace to keep references unique across WordPress and Admin Columns
 */
final class Addon
{
    private static function create(string $separator, string $suffix = ''): string
    {
        $namespace = ['acp', 'data', 'sources'];

        if ($suffix) {
            $namespace[] = $suffix;
        }

        return implode($separator, $namespace);
    }

    public static function slug(string $suffix = ''): string
    {
        return self::create('-', $suffix);
    }

    public static function key(string $suffix = ''): string
    {
        return self::create('_', $suffix);
    }

}
