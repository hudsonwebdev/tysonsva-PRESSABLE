<?php

declare(strict_types=1);

namespace ACA\DataSources\ListTable\SaveHeading;

use AC;
use AC\Table\SaveHeading\ScreenColumnsFactory;
use ACA\DataSources;

class DataSourceFactory extends ScreenColumnsFactory
{
    public function can_create(AC\TableScreen $table_screen): bool
    {
        return $table_screen instanceof DataSources\TableScreen;
    }

}
