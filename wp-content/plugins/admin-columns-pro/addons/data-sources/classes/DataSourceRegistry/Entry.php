<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSourceRegistry;

use AC\Type\Group;
use ACA\DataSources\Admin\Page\MenuFactory;
use ACA\DataSources\Admin\Page\SubmenuFactory;
use ACA\DataSources\Admin\PageFactory;
use ACA\DataSources\Capabilities;
use ACA\DataSources\DataSource;

final class Entry
{
    private DataSource $data_source;

    private ?PageFactory $page_factory = null;

    private ?Capabilities $capabilities = null;

    private ?Group $group = null;

    public function __construct(DataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    public static function create(DataSource $data_source): self
    {
        return new self($data_source);
    }

    public function set_menu(
        string $title,
        ?string $menu_title = null,
        ?string $icon = null,
        ?int $position = null
    ): self {
        $this->page_factory = new MenuFactory($title, $menu_title, $icon, $position);

        return $this;
    }

    public function set_submenu(
        string $title,
        string $parent_slug,
        ?string $menu_title = null,
        ?int $position = null
    ): self {
        $this->page_factory = new SubmenuFactory($title, $parent_slug, $menu_title, $position);

        return $this;
    }

    public function set_group(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function set_capabilities(Capabilities $capabilities): self
    {
        $this->capabilities = $capabilities;

        return $this;
    }

    public function get_data_source(): DataSource
    {
        return $this->data_source;
    }

    public function get_page_factory(): ?PageFactory
    {
        return $this->page_factory;
    }

    public function get_capabilities(): ?Capabilities
    {
        return $this->capabilities;
    }

    public function get_group(): ?Group
    {
        return $this->group;
    }

}
