<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\RelationFactory;

use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Repository\Database\Table;

final class AttributeRelationFactory extends ColumnRelationFactory
{
    private string $foreign_attribute;

    private string $foreign_value_column;

    public function __construct(
        TableRelationFactory $relation,
        string $label,
        string $foreign_attribute,
        string $foreign_value_column
    ) {
        parent::__construct($relation, $label);

        $this->foreign_attribute = $foreign_attribute;
        $this->foreign_value_column = $foreign_value_column;
    }

    public function create(Table $table): Relation
    {
        return new Relation\AttributeRelation(
            $this->relation->create($table),
            $this->label,
            $this->foreign_attribute,
            $this->foreign_value_column
        );
    }

}
