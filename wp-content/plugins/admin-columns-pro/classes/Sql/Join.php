<?php

declare(strict_types=1);

namespace ACP\Sql;

use DomainException;

class Join
{
    private JoinType $type;

    private string $joined_table;

    private array $clauses = [];

    protected function __construct(
        JoinType $type,
        string $joined_table
    ) {
        $this->type = $type;
        $this->joined_table = $joined_table;
    }

    public static function left(string $joined_table, ?string $alias = null): self
    {
        return new self(
            JoinType::left(),
            Identifier::quote_alias($joined_table, $alias)
        );
    }

    public static function left_subquery(string $subquery, string $alias): self
    {
        return new self(
            JoinType::left(),
            '(' . $subquery . ') AS ' . Identifier::quote($alias)
        );
    }

    public static function inner(string $joined_table, ?string $alias = null): self
    {
        return new self(
            JoinType::inner(),
            Identifier::quote_alias($joined_table, $alias)
        );
    }

    public static function inner_subquery(string $subquery, string $alias): self
    {
        return new self(
            JoinType::inner(),
            '(' . $subquery . ') AS ' . Identifier::quote($alias)
        );
    }

    /**
     * Can be chained: $join->on($left, $operator, $right)->on($left, $operator, $right);
     */
    public function on(string $left, string $operator, string $right): self
    {
        $this->clauses[] = sprintf(
            '%s %s %s',
            $left,
            $operator,
            $right
        );

        return $this;
    }

    public function get_sql(): string
    {
        if ($this->clauses === []) {
            throw new DomainException('At least one join condition is required.');
        }

        return sprintf(
            '%s JOIN %s ON %s',
            $this->type,
            $this->joined_table,
            implode("\nAND ", $this->clauses)
        );
    }

    public function __toString(): string
    {
        return $this->get_sql();
    }

}
