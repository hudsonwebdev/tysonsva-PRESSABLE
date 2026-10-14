<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC\Type\Group;
use AC\Vendor\DI\Container;
use ACA\DataSources\DataSource\Relation;

final class RelationColumnFactoryResolver
{
    private Container $container;

    private ColumnFactoryMap $map;

    public function __construct(Container $container, ColumnFactoryMap $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function create(
        Relation\ColumnRelation $relation,
        DataSource $data_source,
        Group $group
    ): ColumnFactory {
        return $this->container->make($this->map->get(get_class($relation)), [
            'id'          => $relation->get_id(),
            'label'       => $relation->get_label(),
            'data_source' => $data_source,
            'group'       => $group,
            'relation'    => $relation,
        ]);
    }

}
