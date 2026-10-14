<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\Relation;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Type\Cardinality;
use ACP\Sql\Alias;
use ACP\Sql\JoinType;

class ColumnRelation implements Relation
{
    private Relation $relation;

    private string $label;

    /**
     * Label will be shown in the column select dropdown and,
     * is the default label of the column.
     */
    public function __construct(Relation $relation, string $label)
    {
        $this->relation = $relation;
        $this->label = $label;
    }

    public function get_id(): string
    {
        return $this->get_local_column() . '_' . $this->get_foreign_data_source()->get_id();
    }

    public function get_label(): string
    {
        return $this->label;
    }

    public function get_cardinality(): Cardinality
    {
        return $this->relation->get_cardinality();
    }

    public function get_local_table(): string
    {
        return $this->relation->get_local_table();
    }

    public function get_local_column(): string
    {
        return $this->relation->get_local_column();
    }

    public function get_foreign_data_source(): DataSource
    {
        return $this->relation->get_foreign_data_source();
    }

    public function get_foreign_table(): string
    {
        return $this->relation->get_foreign_table();
    }

    public function get_foreign_column(): string
    {
        return $this->relation->get_foreign_column();
    }

    public function get_join_type(): JoinType
    {
        return $this->relation->get_join_type();
    }

    public function has_alias(): bool
    {
        return $this->relation->has_alias();
    }

    public function get_alias(): Alias
    {
        return $this->relation->get_alias();
    }

    public function with_alias(Alias $alias): self
    {
        $clone = clone $this;
        $clone->relation = $this->relation->with_alias($alias);

        return $clone;
    }

}
