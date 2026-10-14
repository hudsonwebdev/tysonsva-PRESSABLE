<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

final class ConfigurableType implements ColumnType
{
    private ?string $default_column_type;

    public function __construct(?string $default_column_type = null)
    {
        $this->default_column_type = $default_column_type;
    }

    public static function for(string $name, ?string $default_column_type = null): Config\Column
    {
        return Config\Column::for($name)->with_type(new self($default_column_type));
    }

    public function has_default_column_type(): bool
    {
        return null !== $this->default_column_type;
    }

    public function get_default_column_type(): ?string
    {
        return $this->default_column_type;
    }

}
