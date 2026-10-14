<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade;

use ACA\DataSources\Exception\TableException;
use ACA\DataSources\Repository\Database;
use ACA\DataSources\Repository\Database\Table\Resolver;

final class Table
{
    /**
     * @throws TableException
     */
    public static function from(string $table, ?string $identifier = null): Database\Table
    {
        return (new Resolver())->resolve($table, $identifier);
    }

}
