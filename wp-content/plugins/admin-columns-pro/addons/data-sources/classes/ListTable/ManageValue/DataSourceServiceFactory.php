<?php

declare(strict_types=1);

namespace ACA\DataSources\ListTable\ManageValue;

use AC;
use ACA\DataSources;

class DataSourceServiceFactory implements AC\TableScreen\ManageValueServiceFactory
{
    public function can_create(AC\TableScreen $table_screen): bool
    {
        return $table_screen instanceof DataSources\TableScreen;
    }

    public function create(
        AC\TableScreen $table_screen,
        AC\Table\ManageValue\RenderFactory $factory,
        int $priority = 100
    ): AC\TableScreen\ManageValueService {
        return new DataSource($factory);
    }
}
