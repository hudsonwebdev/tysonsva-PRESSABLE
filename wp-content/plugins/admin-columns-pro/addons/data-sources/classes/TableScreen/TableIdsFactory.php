<?php

declare(strict_types=1);

namespace ACA\DataSources\TableScreen;

use AC;
use AC\Type\TableIdCollection;
use ACA\DataSources\DataSourceRegistry;

final class TableIdsFactory implements AC\TableIdsFactory
{
    private DataSourceRegistry $data_sources;

    public function __construct(DataSourceRegistry $data_sources)
    {
        $this->data_sources = $data_sources;
    }

    public function create(): AC\Type\TableIdCollection
    {
        $collection = new TableIdCollection([]);

        foreach ($this->data_sources as $data_source) {
            $collection->add($data_source->get_id()->to_table_screen_id());
        }

        return $collection;
    }

}
