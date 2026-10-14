<?php

use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource\Relation;

return [
    Relation\ColumnRelation::class    => ColumnFactory\ColumnRelationFactory::class,
    Relation\AttributeRelation::class => ColumnFactory\AttributeRelationFactory::class,
];
