<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC\Collection;
use AC\Type\Group;
use ACA\DataSources\Type\DataSourceId;

/**
 * Used for grouping columns in the UI dropdown when adding a new column.
 */
final class GroupRegistry extends Collection
{
    public function register(DataSourceId $id, Group $group): void
    {
        $this->data[(string)$id] = $group;
    }

    public function find(DataSourceId $id): ?Group
    {
        return $this->data[(string)$id] ?? null;
    }

    public function current(): Group
    {
        return current($this->data);
    }

}
