<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\Relation;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Type\Cardinality;
use ACA\DataSources\Type\ColumnName;
use ACP\Sql\Alias;
use ACP\Sql\JoinType;
use DomainException;

final class TableRelation implements Relation
{
    private Cardinality $cardinality;

    private DataSource $foreign_data_source;

    private ColumnName $foreign_column;

    private string $local_table;

    private ColumnName $local_column;

    private JoinType $join_type;

    private ?Alias $alias;

    public function __construct(
        Cardinality $cardinality,
        DataSource $foreign_data_source,
        string $foreign_column,
        string $local_table,
        string $local_column,
        ?Alias $alias = null,
        ?JoinType $join_type = null
    ) {
        $this->cardinality = $cardinality;
        $this->foreign_data_source = $foreign_data_source;
        $this->foreign_column = ColumnName::from($foreign_column);
        $this->local_table = $local_table;
        $this->local_column = ColumnName::from($local_column);
        $this->join_type = $join_type ?? JoinType::left();
        $this->alias = $alias;
    }

    public function get_cardinality(): Cardinality
    {
        return $this->cardinality;
    }

    public function get_foreign_data_source(): DataSource
    {
        return $this->foreign_data_source;
    }

    public function get_foreign_table(): string
    {
        return $this->foreign_data_source->get_repository()->get_table();
    }

    public function get_foreign_column(): string
    {
        return (string)$this->foreign_column;
    }

    public function get_local_table(): string
    {
        return $this->local_table;
    }

    public function get_local_column(): string
    {
        return (string)$this->local_column;
    }

    public function get_join_type(): JoinType
    {
        return $this->join_type;
    }

    public function has_alias(): bool
    {
        return $this->alias !== null;
    }

    public function get_alias(): Alias
    {
        if (! $this->has_alias()) {
            throw new DomainException('No alias found.');
        }

        return $this->alias;
    }

    public function with_alias(Alias $alias): self
    {
        $clone = clone $this;
        $clone->alias = $alias;

        return $clone;
    }

}
