<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use AC\Collection;
use ACA\DataSources\DataSource\Relation\ColumnRelation;
use ACA\DataSources\DataSource\Relation\TableRelation;

final class Relations extends Collection
{
    public function __construct(array $relations = [])
    {
        foreach ($relations as $relation) {
            $this->add($relation);
        }
    }

    private function add(Relation $relation): void
    {
        $this->data[] = $relation;
    }

    public function current(): Relation
    {
        return current($this->data);
    }

    public function get_table_relations(): self
    {
        return $this->filter_by_type(TableRelation::class);
    }

    public function get_column_relations(): self
    {
        return $this->filter_by_type(ColumnRelation::class);
    }

    private function filter_by_type(string $type): self
    {
        $data = [];

        foreach ($this as $relation) {
            if ($relation instanceof $type) {
                $data[] = $relation;
            }
        }

        return new self($data);
    }

}
