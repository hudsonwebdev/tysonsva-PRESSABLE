<?php

declare(strict_types=1);

namespace ACA\DataSources;

use ACA\DataSources\Repository\Database\Table;

interface RepositoryFactory
{
    public function create(Table $table): Repository;

}
