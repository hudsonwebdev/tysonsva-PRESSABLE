<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\RelationFactory;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\DataSource\RelationFactory;
use ACA\DataSources\Repository\Database\Table;
use ACA\DataSources\Type\Cardinality;
use ACA\DataSources\Type\ColumnName;
use ACP\Sql\Alias;
use ACP\Sql\JoinType;
use DomainException;

class TableRelationFactory implements RelationFactory
{
    private Cardinality $cardinality;

    private DataSource $foreign_data_source;

    private ColumnName $foreign_column;

    private ?ColumnName $local_column;

    private JoinType $join_type;

    private ?Alias $alias;

    private function __construct(
        Cardinality $cardinality,
        DataSource $foreign_data_source,
        string $foreign_column,
        ?string $local_column = null,
        ?Alias $alias = null,
        ?JoinType $join_type = null
    ) {
        $this->cardinality = $cardinality;
        $this->foreign_data_source = $foreign_data_source;
        $this->foreign_column = ColumnName::from($foreign_column);
        $this->local_column = $local_column !== null
            ? ColumnName::from($local_column)
            : null;
        $this->join_type = $join_type ?? JoinType::left();
        $this->alias = $alias;

        $this->validate();
    }

    public static function has_one(
        DataSource $foreign_data_source,
        string $foreign_column,
        ?string $local_column = null,
        ?Alias $alias = null,
        ?JoinType $join_type = null
    ): self {
        return new self(
            Cardinality::has_one(),
            $foreign_data_source,
            $foreign_column,
            $local_column,
            $alias,
            $join_type
        );
    }

    public static function has_many(
        DataSource $foreign_data_source,
        string $foreign_column,
        ?string $local_column = null,
        ?Alias $alias = null,
        ?JoinType $join_type = null
    ): self {
        return new self(
            Cardinality::has_many(),
            $foreign_data_source,
            $foreign_column,
            $local_column,
            $alias,
            $join_type
        );
    }

    private function validate(): void
    {
        if (! $this->foreign_data_source->get_columns()->has((string)$this->foreign_column)) {
            throw new DomainException(
                sprintf(
                    'Foreign column `%s` not found.',
                    $this->foreign_column
                )
            );
        }
    }

    public function with_alias(Alias $alias): self
    {
        $clone = clone $this;
        $clone->alias = $alias;

        return $clone;
    }

    public function as_inner_join(): self
    {
        $clone = clone $this;
        $clone->join_type = JoinType::inner();

        return $clone;
    }

    public function create(Table $table): Relation
    {
        $local_column = $this->local_column !== null
            ? (string)$this->local_column
            : $table->get_identifier()->get_name();

        return new Relation\TableRelation(
            $this->cardinality,
            $this->foreign_data_source,
            (string)$this->foreign_column,
            $table->get_name(),
            $local_column,
            $this->alias,
            $this->join_type
        );
    }

}
