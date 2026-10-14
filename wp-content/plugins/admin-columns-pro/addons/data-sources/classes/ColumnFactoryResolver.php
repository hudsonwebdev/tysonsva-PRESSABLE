<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC\Type\Group;
use AC\Vendor\DI\Container;
use ACA\DataSources\DataSource\ColumnType;
use ACP\Sql\Alias;

final class ColumnFactoryResolver
{
    private Container $container;

    private ColumnFactoryMap $map;

    public function __construct(Container $container, ColumnFactoryMap $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function create(
        string $id,
        string $label,
        ColumnType $column_type,
        DataSource $data_source,
        Group $group,
        ?Alias $alias = null
    ): ColumnFactory {
        return $this->container->make($this->map->get(get_class($column_type)), [
            'id'          => $id,
            'label'       => $label,
            'data_source' => $data_source,
            'group'       => $group,
            'alias'       => $alias,
            'column_type' => $column_type,
        ]);
    }

}
