<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository;

use ACA\DataSources\Repository\Database\Table;
use ACA\DataSources\RepositoryFactory;

final class DatabaseFactory implements RepositoryFactory
{
    public function create(Table $table): Database
    {
        return new Database($table);
    }

}
