<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use ACA\DataSources\Setting\ColumnTypeOptionMap;

final class ColumnTypeSelectResolver
{
    private ColumnTypeOptionMap $map;

    public function __construct(ColumnTypeOptionMap $map)
    {
        $this->map = $map;
    }

    public function resolve(?string $default_column_type = null): ColumnTypeSelect
    {
        return new ColumnTypeSelect($this->map, $default_column_type);
    }

}
