<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin;

use ACA\DataSources\Addon;
use ACA\DataSources\Type\DataSourceId;

class Page
{
    protected DataSourceId $data_source_id;

    protected string $slug;

    protected string $title;

    protected string $parent_slug;

    protected string $menu_title;

    protected ?int $position;

    public function __construct(
        DataSourceId $data_source_id,
        string $title,
        string $parent_slug,
        ?string $menu_title = null,
        ?int $position = null
    ) {
        $this->data_source_id = $data_source_id;
        $this->slug = Addon::slug((string)$data_source_id);
        $this->title = $title;
        $this->parent_slug = $parent_slug;
        $this->menu_title = $menu_title ?? $title;
        $this->position = $position;
    }

    public function get_data_source_id(): DataSourceId
    {
        return $this->data_source_id;
    }

    public function get_slug(): string
    {
        return $this->slug;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_menu_title(): string
    {
        return $this->menu_title;
    }

    public function get_position(): ?int
    {
        return $this->position;
    }

    public function get_parent_slug(): string
    {
        return $this->parent_slug;
    }

    public function get_screen_id(): string
    {
        return get_plugin_page_hookname($this->slug, $this->parent_slug);
    }

}
