<?php

declare(strict_types=1);

namespace ACA\DataSources\CapabilitiesFactory;

use ACA\DataSources\CapabilitiesFactory;
use ACA\DataSources\DataSource;

/**
 * Protect WordPress core tables from editing and deleting.
 */
final class ReadOnlyCoreTables extends ReadOnlyTables
{
    public function __construct(CapabilitiesFactory $capabilities_factory, DataSource $data_source)
    {
        global $wpdb;

        parent::__construct(
            $capabilities_factory,
            $data_source,
            $wpdb->tables()
        );
    }

}
