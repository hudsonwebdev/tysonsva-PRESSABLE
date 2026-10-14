<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade;

use ACA\DataSources\DataSource\Config;
use ACA\DataSources\DataSource\RelationFactoryCollection;
use ACA\DataSources\DataSourceRegistry;
use ACA\DataSources\Exception\TableException;

final class Entry
{
    /**
     * @throws TableException
     */
    public static function from(
        string $table,
        string $data_source_id,
        ?Config\Columns $config = null,
        ?RelationFactoryCollection $relations = null
    ): DataSourceRegistry\Entry {
        return new DataSourceRegistry\Entry(
            DataSource::from($table, $data_source_id, $config, $relations)
        );
    }

}
