<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

final class IdentifierType implements ColumnType
{
    public static function for(string $name): Config\Column
    {
        return Config\Column::for($name)->with_type(new static());
    }

}
