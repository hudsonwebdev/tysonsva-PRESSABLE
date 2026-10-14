<?php

declare(strict_types=1);

namespace ACA\DataSources;

use DomainException;

final class ListTableFactory
{
    private DataSourceRegistry $data_sources;

    public function __construct(DataSourceRegistry $data_sources)
    {
        $this->data_sources = $data_sources;
    }

    public function can_create(string $slug): bool
    {
        return $this->data_sources->find_page_by_slug($slug) !== null;
    }

    public function create(string $slug): ListTable
    {
        $page = $this->data_sources->find_page_by_slug($slug);

        if (! $page) {
            throw new DomainException(sprintf('Cannot create from slug `%s`.', $slug));
        }

        return new ListTable(
            $this->data_sources->get($page->get_data_source_id()),
            $slug,
            $page->get_screen_id()
        );
    }

}
