<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\RelationFactory;

use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\DataSource\RelationFactory;
use ACA\DataSources\Repository\Database\Table;

class ColumnRelationFactory implements RelationFactory
{
    protected TableRelationFactory $relation;

    protected string $label;

    public function __construct(TableRelationFactory $relation, string $label)
    {
        $this->relation = $relation;
        $this->label = $label;
    }

    public function create(Table $table): Relation
    {
        return new Relation\ColumnRelation(
            $this->relation->create($table),
            $this->label
        );
    }

}
