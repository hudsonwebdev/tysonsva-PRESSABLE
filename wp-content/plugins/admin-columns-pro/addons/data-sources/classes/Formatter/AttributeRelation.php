<?php

declare(strict_types=1);

namespace ACA\DataSources\Formatter;

use ACA\DataSources\DataSource;
use ACA\DataSources\Repository;
use ACA\DataSources\Sql\TableAwareQueryBuilder;

class AttributeRelation extends Relation
{
    protected DataSource\Relation\AttributeRelation $attribute_relation;

    private string $attribute;

    public function __construct(
        string $id,
        string $column,
        string $attribute,
        DataSource\Relation\AttributeRelation $relation
    ) {
        parent::__construct(
            $id,
            $column,
            $relation,
        );

        $this->attribute_relation = $relation;
        $this->attribute = $attribute;
    }

    protected function get_builder(array $row, Repository $repository): TableAwareQueryBuilder
    {
        $builder = parent::get_builder($row, $repository);
        $builder->and_where(
            $this->attribute_relation->get_foreign_attribute(),
            $this->attribute
        );

        return $builder;
    }

}
