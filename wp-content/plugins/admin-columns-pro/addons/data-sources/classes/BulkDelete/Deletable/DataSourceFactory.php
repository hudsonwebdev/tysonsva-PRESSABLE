<?php

declare(strict_types=1);

namespace ACA\DataSources\BulkDelete\Deletable;

use AC;
use ACA\DataSources;
use ACA\DataSources\CapabilitiesChecker;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Editing\BulkDelete\StrategyFactory;

class DataSourceFactory implements StrategyFactory
{
    private CapabilitiesChecker $capabilities_checker;

    public function __construct(CapabilitiesChecker $capabilities_checker)
    {
        $this->capabilities_checker = $capabilities_checker;
    }

    public function create(AC\TableScreen $table_screen): ?Deletable
    {
        if (! $table_screen instanceof DataSources\TableScreen) {
            return null;
        }

        return new DataSource(
            $table_screen->get_data_source(),
            $this->capabilities_checker
        );
    }

}
