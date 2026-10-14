<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC\ListScreen;
use ACA\GravityForms\TableScreen;

final class GridColumnMetaSync
{
    private GridColumnMetaScope $scope;

    public function __construct(GridColumnMetaScope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * Align GF's grid column meta with the given AC list screen's columns.
     */
    public function sync(TableScreen\Entry $table_screen, ListScreen $list_screen): void
    {
        $this->scope->write_for_list_screen($table_screen, $list_screen);
    }

}
