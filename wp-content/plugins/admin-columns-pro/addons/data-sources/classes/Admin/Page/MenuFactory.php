<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin\Page;

use ACA\DataSources\Admin\PageFactory;
use ACA\DataSources\Type\DataSourceId;

final class MenuFactory implements PageFactory
{
    private string $title;

    private ?string $menu_title;

    private ?string $icon;

    private ?int $position;

    public function __construct(
        string $title,
        ?string $menu_title = null,
        ?string $icon = null,
        ?int $position = null
    ) {
        $this->title = $title;
        $this->menu_title = $menu_title;
        $this->icon = $icon;
        $this->position = $position;
    }

    public function create(DataSourceId $id): Menu
    {
        return new Menu(
            $id,
            $this->title,
            $this->menu_title,
            $this->icon,
            $this->position,
        );
    }

}
