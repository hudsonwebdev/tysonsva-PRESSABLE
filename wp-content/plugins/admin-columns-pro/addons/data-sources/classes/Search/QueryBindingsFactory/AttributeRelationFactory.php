<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\QueryBindingsFactory;

use ACA\DataSources\DataSource;
use ACA\DataSources\Search\QueryBindingsFactory;

final class AttributeRelationFactory
{
    public function create(
        DataSource\Relation\AttributeRelation $relation,
        string $id,
        string $attribute
    ): QueryBindingsFactory {
        return new AttributeRelation($relation, $id, $attribute);
    }

}
