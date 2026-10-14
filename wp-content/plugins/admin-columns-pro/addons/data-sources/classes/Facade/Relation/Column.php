<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade\Relation;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\RelationFactory\ColumnRelationFactory;
use ACA\DataSources\DataSource\RelationFactory\TableRelationFactory;

final class Column
{
    public static function has_one(
        DataSource $foreign_data_source,
        string $foreign_column,
        string $label,
        ?string $local_column = null
    ): ColumnRelationFactory {
        return new ColumnRelationFactory(
            TableRelationFactory::has_one($foreign_data_source, $foreign_column, $local_column),
            $label
        );
    }

    public static function has_many(
        DataSource $foreign_data_source,
        string $foreign_column,
        string $label,
        ?string $local_column = null
    ): ColumnRelationFactory {
        return new ColumnRelationFactory(
            TableRelationFactory::has_many($foreign_data_source, $foreign_column, $local_column),
            $label
        );
    }

}
