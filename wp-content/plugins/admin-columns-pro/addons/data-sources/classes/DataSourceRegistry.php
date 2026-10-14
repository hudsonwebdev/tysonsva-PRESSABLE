<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC\Collection;
use AC\Type\Group;
use ACA\DataSources\Admin\Page;
use ACA\DataSources\Admin\PageRegistry;
use ACA\DataSources\DataSourceRegistry\Entry;
use ACA\DataSources\Type\DataSourceId;
use DomainException;

final class DataSourceRegistry extends Collection
{
    private GroupRegistry $group_registry;

    private PageRegistry $pages;

    private CapabilitiesChecker $capabilities;

    public function __construct(
        GroupRegistry $group_registry,
        PageRegistry $pages,
        CapabilitiesChecker $capabilities
    ) {
        $this->group_registry = $group_registry;
        $this->pages = $pages;
        $this->capabilities = $capabilities;
    }

    public function register(Entry $entry): void
    {
        $data_source = $entry->get_data_source();
        $id = $data_source->get_id();

        // Check for duplicate entry
        if ($this->data[(string)$id] ?? null) {
            throw new DomainException(sprintf('Entry with id `%s` already exists.', $id));
        }

        // Register Data Source
        $this->data[(string)$id] = $data_source;

        // Register Page
        $page_factory = $entry->get_page_factory();
        $page = $page_factory
            ? $page_factory->create($id)
            : null;

        if ($page) {
            $this->pages->register($page);
        }

        // Register Capabilities
        if ($entry->get_capabilities()) {
            $this->capabilities->register_capabilities(
                $id,
                $entry->get_capabilities()
            );
        }

        // Register Group
        $group = $entry->get_group();

        if ($group === null) {
            $label = $page ? $page->get_menu_title() : (string)$id;
            $group = new Group((string)$id, $label);
        }

        $this->group_registry->register($id, $group);
    }

    public function current(): DataSource
    {
        return current($this->data);
    }

    // Getters

    public function has(DataSourceId $id): bool
    {
        return $this->find($id) !== null;
    }

    public function get(DataSourceId $id): DataSource
    {
        $data_source = $this->find($id);

        if (! $data_source) {
            throw new DomainException(sprintf('Data source with id %s does not exist.', $id));
        }

        return $data_source;
    }

    private function find(DataSourceId $id): ?DataSource
    {
        return $this->data[(string)$id] ?? null;
    }

    // Domain objects

    public function get_group(DataSourceId $id): Group
    {
        $group = $this->group_registry->find($id);

        if (! $group) {
            throw new DomainException('Expected group. Found none.');
        }

        return $group;
    }

    public function has_page(DataSourceId $id): bool
    {
        return $this->pages->has_data_source($id);
    }

    public function get_page(DataSourceId $id): Page
    {
        return $this->pages->get_by_data_source($id);
    }

    // Domain object helpers

    public function find_page_by_slug(string $slug): ?Page
    {
        return $this->pages->find($slug);
    }

}
