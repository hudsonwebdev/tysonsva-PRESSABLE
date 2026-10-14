<?php

declare(strict_types=1);

namespace ACA\DataSources;

use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Type\DataSourceId;

class RelationRegistry
{
    /** @var array<string, Relation[]> */
    private array $data = [];

    public function register(DataSourceId $id, Relation $relation): void
    {
        $this->data[(string)$id][] = $relation;
    }

    /**
     * @return Relation[]
     */
    public function get_relations(DataSourceId $id): array
    {
        return $this->data[(string)$id] ?? [];
    }

    public function has_relations(DataSourceId $id): bool
    {
        return ! empty($this->data[(string)$id]);
    }

}
