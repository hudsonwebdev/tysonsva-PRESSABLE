<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC;
use AC\Registerable;
use ACA\DataSources;
use ACA\DataSources\Addon;
use ACA\DataSources\Asset\Script\DataSourceTable;
use ACP\Settings\ListScreen\TableElement;
use ACP\Settings\ListScreen\TableElements;

final class TableScreen implements Registerable
{
    private AC\Asset\Location $location;

    public function __construct(AC\Asset\Location $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'table_scripts']);
        add_action('ac/admin/settings/table_elements', [$this, 'disable_table_elements'], 10, 2);
    }

    public function table_scripts(AC\ListScreen $list_screen): void
    {
        if (! $list_screen->get_table_screen() instanceof DataSources\TableScreen) {
            return;
        }

        (new DataSourceTable(
            $this->location->with_suffix('/assets/js/table.js')
        ))->enqueue();

        (new AC\Asset\Style(
            Addon::slug('table-style'),
            $this->location->with_suffix('/assets/css/table.css')
        ))->enqueue();
    }

    public function disable_table_elements(TableElements $elements, AC\TableScreen $table_screen): void
    {
        if (! $table_screen instanceof DataSources\TableScreen) {
            return;
        }

        $elements->remove(
            new TableElement\BulkActions()
        );

        $elements->remove(
            new TableElement\RowActions()
        );
    }

}
