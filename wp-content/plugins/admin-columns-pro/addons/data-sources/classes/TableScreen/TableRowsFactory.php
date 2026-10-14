<?php

declare(strict_types=1);

namespace ACA\DataSources\TableScreen;

use AC;
use ACA\DataSources\TableScreen;

class TableRowsFactory implements AC\TableScreen\TableRowsFactory
{
    public function create(AC\TableScreen $table_screen): ?AC\TableScreen\TableRows
    {
        if (! $table_screen instanceof TableScreen) {
            return null;
        }

        return new TableRows($table_screen);
    }

}
