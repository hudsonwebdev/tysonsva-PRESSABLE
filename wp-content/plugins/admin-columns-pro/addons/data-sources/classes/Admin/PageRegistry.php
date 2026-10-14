<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin;

use AC\Collection;
use ACA\DataSources\Type\DataSourceId;
use DomainException;

final class PageRegistry extends Collection
{
    public function register(Page $page): void
    {
        $this->data[$page->get_slug()] = $page;
    }

    public function find(string $slug): ?Page
    {
        return $this->data[$slug] ?? null;
    }

    public function has_screen_id(string $screen_id): bool
    {
        return $this->find_by_screen_id($screen_id) !== null;
    }

    public function get_by_screen_id(string $screen_id): Page
    {
        $page = $this->find_by_screen_id($screen_id);

        if (! $page) {
            throw new DomainException(sprintf('No page found for `%s`.', $screen_id));
        }

        return $page;
    }

    private function find_by_screen_id(string $screen_id): ?Page
    {
        foreach ($this as $page) {
            if ($page->get_screen_id() === $screen_id) {
                return $page;
            }
        }

        return null;
    }

    public function has_data_source(DataSourceId $id): bool
    {
        return $this->find_by_data_source($id) !== null;
    }

    public function get_by_data_source(DataSourceId $id): Page
    {
        $data_source = $this->find_by_data_source($id);

        if (! $data_source) {
            throw new DomainException(sprintf('No Page related to id `%s`.', $id));
        }

        return $data_source;
    }

    private function find_by_data_source(DataSourceId $id): ?Page
    {
        foreach ($this->data as $page) {
            if ($page->get_data_source_id()->equals($id)) {
                return $page;
            }
        }

        return null;
    }

    public function current(): Page
    {
        return current($this->data);
    }

}
