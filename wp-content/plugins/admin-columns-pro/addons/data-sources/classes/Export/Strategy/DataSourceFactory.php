<?php

declare(strict_types=1);

namespace ACA\DataSources\Export\Strategy;

use AC;
use ACA\DataSources;
use ACP;
use ACP\Export\Exporter\TableDataFactory;
use ACP\Export\ResponseFactory;
use ACP\Export\Strategy;

class DataSourceFactory implements ACP\Export\StrategyFactory
{
    private ResponseFactory $response_factory;

    public function __construct(ResponseFactory $response_factory)
    {
        $this->response_factory = $response_factory;
    }

    public function create(AC\TableScreen $table_screen): ?Strategy
    {
        if (! $table_screen instanceof DataSources\TableScreen) {
            return null;
        }

        return new DataSource(
            $this->response_factory,
            new TableDataFactory($table_screen),
            $table_screen
        );
    }

}
