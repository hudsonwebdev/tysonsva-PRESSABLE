<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnType;

use ACA\DataSources\DataSource\ColumnType;
use ACA\DataSources\DataSource\Config;

class WordPressPostType implements ColumnType
{
    private array $post_types;

    public function __construct(array $post_types = [])
    {
        $this->post_types = $post_types;
    }

    public static function for(string $name, array $post_types = []): Config\Column
    {
        return Config\Column::for($name)->with_type(new self($post_types));
    }

    public function get_post_types(): array
    {
        return $this->post_types;
    }

}
