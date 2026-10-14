<?php

declare(strict_types=1);

namespace ACP\Sql;

use ACP\Sql\Expression\Comparison;
use InvalidArgumentException;

class QueryBuilder
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    private array $select = [];

    private string $from = '';

    private array $joins = [];

    private array $where = [];

    private string $group_by = '';

    private string $order_by = '';

    private string $limit = '';

    public static function create(): self
    {
        return new self();
    }

    public static function for(string $table, ?string $alias = null): self
    {
        return (new self())->from($table, $alias);
    }

    public function select(?string $select = null): self
    {
        $this->select = [$select ?? '*'];

        return $this;
    }

    public function select_columns(array $columns): self
    {
        $this->select = $columns;

        return $this;
    }

    public function add_select_columns(array $columns): self
    {
        $this->select = array_merge($this->select, $columns);

        return $this;
    }

    public function add_select(string $select): self
    {
        $this->select[] = $select;

        return $this;
    }

    public function from(string $from, ?string $alias = null): self
    {
        $this->from = 'FROM ' . Identifier::quote_alias($from, $alias);

        return $this;
    }

    public function from_subquery(string $subquery, string $alias): self
    {
        $this->from = sprintf(
            'FROM (%s) AS %s',
            $subquery,
            Identifier::quote($alias)
        );

        return $this;
    }

    public function join(
        string $table,
        $first,
        ?string $operator = null,
        ?string $second = null,
        ?string $alias = null
    ): self {
        return $this->add_join(
            Join::inner($table, $alias),
            $first,
            $operator,
            $second
        );
    }

    public function left_join(
        string $table,
        $first,
        ?string $operator = null,
        ?string $second = null,
        ?string $alias = null
    ): self {
        return $this->add_join(
            Join::left($table, $alias),
            $first,
            $operator,
            $second
        );
    }

    public function join_raw(string $join): self
    {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Advanced usage for conditional joins:
     *
     * $query->join('foreign_table', function (Join $join) {
     *     $join->on('local_table.column', '=', 'foreign_table.column');
     * });
     *
     * @param string|callable $first The left side of a join condition, or a callback that configures a Join.
     */
    private function add_join(
        Join $join,
        $first,
        ?string $operator = null,
        ?string $second = null
    ): self {
        if (is_callable($first)) {
            if ($operator !== null || $second !== null) {
                throw new InvalidArgumentException('Cannot combine join callback with basic join arguments.');
            }

            $first($join);
        } else {
            if (! is_string($first) || $operator === null || $second === null) {
                throw new InvalidArgumentException('A join requires a left side, an operator, and a right side.');
            }

            $join->on($first, $operator, $second);
        }

        $this->joins[] = $join;

        return $this;
    }

    /**
     * Usage:
     *
     * With explicit operator "column = value" or "column > value"
     * Or shorthand "column = value" written as $query->where(column, value)
     */
    public function where(string $column, $operator, $value = null): self
    {
        $this->where_raw($this->create_expression($column, $operator, $value));

        return $this;
    }

    public function and_where(string $column, $operator, $value = null): self
    {
        $this->and_where_raw($this->create_expression($column, $operator, $value));

        return $this;
    }

    public function or_where(string $column, $operator, $value = null): self
    {
        $this->or_where_raw($this->create_expression($column, $operator, $value));

        return $this;
    }

    public function where_raw(string $where): self
    {
        $this->where = ['WHERE ' . $where];

        return $this;
    }

    public function and_where_raw(string $where): self
    {
        if (! $this->where) {
            $this->where_raw($where);
        } else {
            $this->where[] = 'AND ' . $where;
        }

        return $this;
    }

    public function or_where_raw(string $where): self
    {
        if (! $this->where) {
            $this->where_raw($where);
        } else {
            $this->where[] = 'OR ' . $where;
        }

        return $this;
    }

    public function where_in(string $column, array $values): self
    {
        $this->where_raw($this->create_in_expression($column, $values));

        return $this;
    }

    public function and_where_in(string $column, array $values): self
    {
        $this->and_where_raw($this->create_in_expression($column, $values));

        return $this;
    }

    public function or_where_in(string $column, array $values): self
    {
        $this->or_where_raw($this->create_in_expression($column, $values));

        return $this;
    }

    private function create_in_expression(string $column, array $values): string
    {
        return (string)Comparison::in($column, $values);
    }

    private function create_expression(string $column, $operator, $value = null): string
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        return (string)Comparison::comparison($column, $operator, $value);
    }

    public function group_by(string $group_by): self
    {
        $this->group_by = $group_by;

        return $this;
    }

    public function group_by_column(string $column): self
    {
        return $this->group_by($column);
    }

    public function order_by(?string $order_by = null): self
    {
        $this->order_by = (string)$order_by;

        return $this;
    }

    public function order_by_column(string $column, ?string $order = null): self
    {
        if ($order !== self::DESC) {
            $order = self::ASC;
        }

        return $this->order_by($column . ' ' . $order);
    }

    public function limit(?int $limit = null, int $offset = 0): self
    {
        $expression = 'LIMIT ' . $limit . ' OFFSET ' . $offset;

        if ($limit === null) {
            $expression = '';
        }

        $this->limit = $expression;

        return $this;
    }

    public function get_select(): string
    {
        return $this->select
            ? 'SELECT ' . implode(",\n", $this->select)
            : '';
    }

    public function get_from(): string
    {
        return $this->from;
    }

    public function get_joins(): string
    {
        return implode("\n", $this->joins);
    }

    public function get_where(): string
    {
        return implode("\n", $this->where);
    }

    public function get_group_by(): string
    {
        return $this->group_by
            ? 'GROUP BY ' . $this->group_by
            : '';
    }

    public function get_order_by(): string
    {
        return $this->order_by
            ? 'ORDER BY ' . $this->order_by
            : '';
    }

    public function get_limit(): string
    {
        return $this->limit;
    }

    public function get_sql(): string
    {
        return trim(
            implode("\n", array_filter([
                $this->get_select(),
                $this->get_from(),
                $this->get_joins(),
                $this->get_where(),
                $this->get_group_by(),
                $this->get_order_by(),
                $this->get_limit(),
            ]))
        );
    }

    public function one(): array
    {
        global $wpdb;

        $this->limit(1);

        return (array)$wpdb->get_row($this->get_sql(), ARRAY_A);
    }

    public function all(): array
    {
        global $wpdb;

        return (array)$wpdb->get_results($this->get_sql(), ARRAY_A);
    }

    /**
     * HAVING with SELECT columns won't work because of COUNT(*) wrapper
     */
    public function count(): int
    {
        global $wpdb;

        $clone = clone $this;
        $clone
            ->select('1')
            ->order_by()
            ->limit();

        return (int)$wpdb->get_var(
            self::create()
                ->select('COUNT(*)')
                ->from_subquery($clone->get_sql(), 'query_to_count')
                ->get_sql()
        );
    }

}
