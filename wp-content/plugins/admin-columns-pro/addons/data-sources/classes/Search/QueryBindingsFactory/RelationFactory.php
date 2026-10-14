<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\QueryBindingsFactory;

use ACA\DataSources\DataSource;
use ACA\DataSources\Search\QueryBindingsFactory;

final class RelationFactory
{
    public function create(
        DataSource\Relation $relation,
        string $id
    ): QueryBindingsFactory {
        return new Relation($relation, $id);
    }

}
