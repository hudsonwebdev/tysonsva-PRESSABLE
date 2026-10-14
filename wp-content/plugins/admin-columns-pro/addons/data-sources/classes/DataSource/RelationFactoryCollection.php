<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use AC\Collection;

class RelationFactoryCollection extends Collection
{
    public function __construct(array $data = [])
    {
        foreach ($data as $relation) {
            $this->add($relation);
        }
    }

    private function add(RelationFactory $relation): void
    {
        $this->data[] = $relation;
    }

    public function current(): RelationFactory
    {
        return current($this->data);
    }

}
