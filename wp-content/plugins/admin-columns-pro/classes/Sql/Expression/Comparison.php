<?php

declare(strict_types=1);

namespace ACP\Sql\Expression;

use ACP\Sql\Expression;
use ACP\Sql\Identifier;

final class Comparison implements Expression
{
    private string $sql;

    private function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public static function comparison(string $column, string $operator, $value): self
    {
        global $wpdb;

        return new self(
            sprintf(
                '%s %s %s',
                Identifier::quote($column),
                $operator,
                $wpdb->prepare('%s', $value)
            )
        );
    }

    public static function eq(string $column, $value): self
    {
        return self::comparison($column, '=', $value);
    }

    public static function neq(string $column, $value): self
    {
        return self::comparison($column, '!=', $value);
    }

    public static function gt(string $column, $value): self
    {
        return self::comparison($column, '>', $value);
    }

    public static function gte(string $column, $value): self
    {
        return self::comparison($column, '>=', $value);
    }

    public static function lt(string $column, $value): self
    {
        return self::comparison($column, '<', $value);
    }

    public static function lte(string $column, $value): self
    {
        return self::comparison($column, '<=', $value);
    }

    public static function in(string $column, array $values): self
    {
        return self::in_expression($column, 'IN', $values);
    }

    public static function not_in(string $column, array $values): self
    {
        return self::in_expression($column, 'NOT IN', $values);
    }

    private static function in_expression(string $column, string $operator, array $values): self
    {
        global $wpdb;

        $placeholders = implode(',', array_fill(0, count($values), '%s'));

        return new self(
            sprintf(
                '%s %s(%s)',
                Identifier::quote($column),
                $operator,
                $wpdb->prepare($placeholders, $values)
            )
        );
    }

    public function __toString(): string
    {
        return $this->sql;
    }

}
