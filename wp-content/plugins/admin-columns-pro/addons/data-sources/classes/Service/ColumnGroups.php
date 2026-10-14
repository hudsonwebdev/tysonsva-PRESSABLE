<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC;
use ACA\DataSources\DataSourceRegistry;

final class ColumnGroups implements AC\Registerable
{
    private DataSourceRegistry $data_sources;

    public function __construct(DataSourceRegistry $data_sources)
    {
        $this->data_sources = $data_sources;
    }

    public function register(): void
    {
        add_action('ac/column/groups', [$this, 'register_column_groups']);
    }

    public function register_column_groups(AC\Type\Groups $groups): void
    {
        foreach ($this->data_sources as $data_source) {
            $groups->add(
                $this->data_sources->get_group($data_source->get_id())
            );
        }
    }

}
