<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\Type\Labels;
use AC\Type\TableId;
use AC\Type\Uri;
use ACA\DataSources\Admin\Page;
use DomainException;
use WP_Screen;

final class TableScreenFactory implements AC\TableScreenFactory
{
    private DataSourceRegistry $data_sources;

    private ListTableFactory $list_table_factory;

    public function __construct(DataSourceRegistry $data_sources, ListTableFactory $list_table_factory)
    {
        $this->data_sources = $data_sources;
        $this->list_table_factory = $list_table_factory;
    }

    private function create_uri(TableId $id, Page $page): Uri
    {
        $parent = $page->get_parent_slug();

        // If parent is not a .php file, route via admin.php
        if (! str_ends_with($parent, '.php')) {
            $parent = 'admin.php';
        }

        return (new Uri(admin_url($parent)))
            ->with_arg('page', (string)$id);
    }

    public function create(TableId $id): AC\TableScreen
    {
        $page = $this->find_page_by_table_id($id);

        if (! $page) {
            throw new DomainException('Cannot create from TableId.');
        }

        $list_table = $this->list_table_factory->create($page->get_slug());

        return new TableScreen(
            $id,
            $this->data_sources->get($page->get_data_source_id()),
            $list_table,
            $page->get_screen_id(),
            new Labels(
                $page->get_title(),
                $page->get_title()
            ),
            $this->create_uri($id, $page)
        );
    }

    public function can_create(TableId $id): bool
    {
        return $this->find_page_by_table_id($id) !== null;
    }

    public function create_from_wp_screen(WP_Screen $screen): AC\TableScreen
    {
        $page = $this->find_page_by_screen_id($screen);

        if (! $page) {
            throw new DomainException(sprintf('Cannot create from WP_Screen with id `%s`.', $screen->id));
        }

        return $this->create(
            $page->get_data_source_id()->to_table_screen_id()
        );
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $this->find_page_by_screen_id($screen) !== null;
    }

    // Query helpers

    private function find_page_by_screen_id(WP_Screen $screen): ?Page
    {
        foreach ($this->data_sources as $data_source) {
            $id = $data_source->get_id();

            if (! $this->data_sources->has_page($id)) {
                continue;
            }

            $page = $this->data_sources->get_page($id);

            if ($page->get_screen_id() === $screen->id) {
                return $page;
            }
        }

        return null;
    }

    private function find_page_by_table_id(TableId $table_id): ?Page
    {
        foreach ($this->data_sources as $data_source) {
            $id = $data_source->get_id();

            if ($this->data_sources->has_page($id) && $id->to_table_screen_id()->equals($table_id)) {
                return $this->data_sources->get_page($id);
            }
        }

        return null;
    }

}
