<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade;

use ACA\DataSources;
use ACA\DataSources\DataSource\Config;
use ACA\DataSources\DataSource\RelationFactoryCollection;
use ACA\DataSources\Exception\TableException;
use ACA\DataSources\Type\DataSourceId;

final class DataSource
{
    /**
     * @throws TableException
     */
    public static function from(
        string $table,
        string $data_source_id,
        ?Config\Columns $config = null,
        ?RelationFactoryCollection $relations = null
    ): DataSources\DataSource {
        return new DataSources\DataSource(
            new DataSourceId($data_source_id),
            Table::from($table),
            $config,
            $relations
        );
    }

}
