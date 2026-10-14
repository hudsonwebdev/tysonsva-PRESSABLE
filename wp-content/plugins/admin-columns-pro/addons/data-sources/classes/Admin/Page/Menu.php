<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin\Page;

use ACA\DataSources\Admin\Page;
use ACA\DataSources\Type\DataSourceId;

final class Menu extends Page
{
    private string $icon;

    public function __construct(
        DataSourceId $data_source_id,
        string $title,
        ?string $menu_title = null,
        ?string $icon = null,
        ?int $position = null
    ) {
        parent::__construct(
            $data_source_id,
            $title,
            'admin.php',
            $menu_title,
            $position ?? 30
        );

        $this->icon = $icon ?? 'dashicons-database';
    }

    public function get_icon(): string
    {
        return $this->icon;
    }

}
