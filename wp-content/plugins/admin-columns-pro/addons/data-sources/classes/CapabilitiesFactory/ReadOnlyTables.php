<?php

declare(strict_types=1);

namespace ACA\DataSources\CapabilitiesFactory;

use ACA\DataSources\Capabilities;
use ACA\DataSources\CapabilitiesFactory;
use ACA\DataSources\DataSource;

/**
 * This decorator can be used to prevent editing and deleting from given tables.
 */
class ReadOnlyTables implements CapabilitiesFactory
{
    private CapabilitiesFactory $capabilities_factory;

    private DataSource $data_source;

    private array $tables;

    public function __construct(
        CapabilitiesFactory $capabilities_factory,
        DataSource $data_source,
        array $tables
    ) {
        $this->capabilities_factory = $capabilities_factory;
        $this->data_source = $data_source;
        $this->tables = $tables;
    }

    public function create(): Capabilities
    {
        $capabilities = $this->capabilities_factory->create();
        $table = $this->data_source->get_repository()->get_table();

        // Add capabilities that are (very) unlikely to exist
        if (in_array($table, $this->tables, true)) {
            return new Capabilities(
                $capabilities->get_read(),
                'ac_restrict_edit_' . $table,
                'ac_restrict_delete_' . $table
            );
        }

        return $capabilities;
    }

}
