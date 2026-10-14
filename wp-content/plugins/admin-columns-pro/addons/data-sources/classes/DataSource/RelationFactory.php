<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use ACA\DataSources\Repository\Database\Table;

interface RelationFactory
{
    public function create(Table $table): Relation;

}
