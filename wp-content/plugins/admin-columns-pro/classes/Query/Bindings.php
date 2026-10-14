<?php

declare(strict_types=1);

namespace ACP\Query;

use ACP\Sql\AliasGenerator;

class Bindings
{
    protected string $where = '';

    protected string $join = '';

    protected string $group_by = '';

    protected string $order_by = '';

    protected string $limits = '';

    protected array $meta_query = [];

    protected string $fields = '';

    /**
     * @deprecated 7.1 Use AliasGenerator::generate() instead.
     */
    public function get_unique_alias(string $column): string
    {
        _deprecated_function(__FUNCTION__, '7.1', 'AliasGenerator::generate()');

        return AliasGenerator::generate($column);
    }

    public function get_where(): string
    {
        return $this->where;
    }

    public function where(string $where): self
    {
        $this->where = $where;

        return $this;
    }

    public function and_where(string $where): self
    {
        if (! $this->where) {
            return $this->where($where);
        }

        $this->where .= "\nAND " . $where;

        return $this;
    }

    public function get_join(): string
    {
        return $this->join;
    }

    public function join(string $join): self
    {
        $this->join = $join;

        return $this;
    }

    public function get_limits(): string
    {
        return $this->limits;
    }

    public function limits(string $limits): self
    {
        $this->limits = $limits;

        return $this;
    }

    public function get_group_by(): string
    {
        return $this->group_by;
    }

    public function group_by(string $group): self
    {
        $this->group_by = $group;

        return $this;
    }

    public function get_order_by(): string
    {
        return $this->order_by;
    }

    public function order_by(string $order_by): self
    {
        $this->order_by = $order_by;

        return $this;
    }

    public function get_meta_query(): array
    {
        return $this->meta_query;
    }

    public function meta_query(array $args): self
    {
        $this->meta_query = $args;

        return $this;
    }

    public function fields(string $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function get_fields(): string
    {
        return $this->fields;
    }

}
