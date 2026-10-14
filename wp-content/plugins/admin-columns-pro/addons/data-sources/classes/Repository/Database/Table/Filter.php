<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table;

/**
 * Decide whether this table column matches the filter criteria
 */
interface Filter
{
    public function matches(Column $column): bool;

}
