<?php

declare(strict_types=1);

namespace ACA\DataSources\Facade\Relation;

use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\RelationFactory\AttributeRelationFactory;
use ACA\DataSources\DataSource\RelationFactory\TableRelationFactory;

final class Attribute
{
    public static function has_one(
        DataSource $foreign_data_source,
        string $foreign_column,
        string $label,
        string $foreign_attribute,
        string $foreign_value_column,
        ?string $local_column = null
    ): AttributeRelationFactory {
        return new AttributeRelationFactory(
            TableRelationFactory::has_one($foreign_data_source, $foreign_column, $local_column),
            $label,
            $foreign_attribute,
            $foreign_value_column
        );
    }

    public static function has_many(
        DataSource $foreign_data_source,
        string $foreign_column,
        string $label,
        string $foreign_attribute,
        string $foreign_value_column,
        ?string $local_column = null
    ): AttributeRelationFactory {
        return new AttributeRelationFactory(
            TableRelationFactory::has_many($foreign_data_source, $foreign_column, $local_column),
            $label,
            $foreign_attribute,
            $foreign_value_column
        );
    }

}
