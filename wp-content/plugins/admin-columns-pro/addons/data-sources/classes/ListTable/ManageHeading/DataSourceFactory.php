<?php

declare(strict_types=1);

namespace ACA\DataSources\ListTable\ManageHeading;

use AC;
use AC\Table\ManageHeading\ScreenColumnsFactory;
use ACA\DataSources;

final class DataSourceFactory extends ScreenColumnsFactory
{
    public function can_create(AC\TableScreen $table_screen): bool
    {
        return $table_screen instanceof DataSources\TableScreen;
    }

}
