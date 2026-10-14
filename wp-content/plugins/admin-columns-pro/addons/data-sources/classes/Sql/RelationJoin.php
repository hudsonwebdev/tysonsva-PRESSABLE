<?php

declare(strict_types=1);

namespace ACA\DataSources\Sql;

use ACA\DataSources\DataSource\Relation;
use ACP\Sql\Identifier;
use ACP\Sql\Join;

class RelationJoin extends Join
{
    public static function from_relation(Relation $relation): self
    {
        $table = $relation->get_foreign_data_source()->get_repository()->get_table();
        $alias = $relation->has_alias() ? (string)$relation->get_alias() : null;

        $join = new self(
            $relation->get_join_type(),
            $alias
                ? Identifier::quote_alias($table, $alias)
                : Identifier::quote($table)
        );

        return $join->on(
            Identifier::quote_qualified($relation->get_local_table(), $relation->get_local_column()),
            '=',
            Identifier::quote_qualified($alias ?? $table, $relation->get_foreign_column())
        );
    }

}
