<?php

declare(strict_types=1);

namespace ACA\DataSources\Query;

use AC;
use ACA\DataSources;
use ACP;

class QueryFactory implements ACP\Query\QueryFactory
{
    public function can_create(AC\TableScreen $table_screen): bool
    {
        return $table_screen instanceof DataSources\TableScreen;
    }

    public function create(array $bindings): ACP\Query
    {
        return new Query($bindings);
    }

}
