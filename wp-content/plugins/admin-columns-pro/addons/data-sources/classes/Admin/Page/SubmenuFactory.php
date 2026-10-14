<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin\Page;

use ACA\DataSources\Admin\Page;
use ACA\DataSources\Admin\PageFactory;
use ACA\DataSources\Type\DataSourceId;

final class SubmenuFactory implements PageFactory
{
    private string $title;

    private string $parent_slug;

    private ?string $menu_title;

    private ?int $position;

    public function __construct(
        string $title,
        string $parent_slug,
        ?string $menu_title = null,
        ?int $position = null
    ) {
        $this->title = $title;
        $this->parent_slug = $parent_slug;
        $this->menu_title = $menu_title;
        $this->position = $position;
    }

    public function create(DataSourceId $id): Page
    {
        return new Page(
            $id,
            $this->title,
            $this->parent_slug,
            $this->menu_title,
            $this->position
        );
    }

}
