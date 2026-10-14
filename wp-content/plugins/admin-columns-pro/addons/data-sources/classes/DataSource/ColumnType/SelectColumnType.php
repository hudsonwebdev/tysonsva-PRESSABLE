<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

class SelectColumnType implements ColumnType
{
    private array $options;

    private bool $is_multiple;

    private bool $lock_options;

    public function __construct(
        array $options = [],
        bool $is_multiple = false,
        bool $lock_options = false
    ) {
        $this->options = $options;
        $this->is_multiple = $is_multiple;
        $this->lock_options = $lock_options;
    }

    public static function for(
        string $name,
        array $options = [],
        bool $is_multiple = false,
        bool $lock_options = false
    ): Config\Column {
        return Config\Column::for($name)->with_type(
            new self($options, $is_multiple, $lock_options)
        );
    }

    public function get_options(): array
    {
        return $this->options;
    }

    public function is_multiple(): bool
    {
        return $this->is_multiple;
    }

    public function is_lock_options(): bool
    {
        return $this->lock_options;
    }

}
