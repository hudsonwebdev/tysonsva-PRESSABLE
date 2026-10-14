<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use AC\Type\ToggleOptions;
use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

class BooleanType implements ColumnType
{
    private ?ToggleOptions $toggle_options;

    public function __construct(?ToggleOptions $toggle_options = null)
    {
        $this->toggle_options = $toggle_options;
    }

    public static function for(string $name, ?ToggleOptions $toggle_options = null): Config\Column
    {
        return Config\Column::for($name)->with_type(new self($toggle_options));
    }

    public function has_options(): bool
    {
        return null !== $this->toggle_options;
    }

    public function get_options(): ?ToggleOptions
    {
        return $this->toggle_options;
    }

}
