<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

class DateTimeType implements ColumnType
{
    private ?string $date_save_format;

    public function __construct(?string $date_save_format = null)
    {
        $this->date_save_format = $date_save_format;
    }

    public static function for(string $name, ?string $date_save_format = null): Config\Column
    {
        return Config\Column::for($name)->with_type(new self($date_save_format));
    }

    public function has_date_save_format(): bool
    {
        return $this->date_save_format !== null;
    }

    public function get_date_save_format(): ?string
    {
        return $this->date_save_format;
    }

}
