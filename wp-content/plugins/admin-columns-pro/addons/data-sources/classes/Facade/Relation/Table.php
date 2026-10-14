<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade\Relation;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\RelationFactory\TableRelationFactory;
use ACP\Sql\Alias;
use ACP\Sql\JoinType;

/**
 * Does not support has_many, because WordPress ListTables group on identifier.
 *
 * Solutions are using a Column- or Attribute relation or reverse the relation where you make a ListTable
 * starting from the table that contains many and join the table that contains one. (1:M becomes M:1)
 *
 * M:M relations a ListTable are not supported.
 */
final class Table
{
    public static function has_one(
        DataSource $foreign_data_source,
        string $foreign_column,
        ?string $local_column = null,
        ?Alias $alias = null,
        ?JoinType $join_type = null
    ): TableRelationFactory {
        return TableRelationFactory::has_one(
            $foreign_data_source,
            $foreign_column,
            $local_column,
            $alias,
            $join_type
        );
    }

}
