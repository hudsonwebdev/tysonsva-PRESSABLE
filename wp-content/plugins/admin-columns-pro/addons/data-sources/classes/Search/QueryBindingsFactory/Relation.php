<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\QueryBindingsFactory;

use ACA\DataSources\DataSource;
use ACA\DataSources\Search\Comparison;
use ACA\DataSources\Search\QueryBindingsFactory;
use ACA\DataSources\Sql\RelationJoin;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\Alias;
use ACP\Sql\Identifier;

class Relation implements QueryBindingsFactory
{
    protected DataSource\Relation $relation;

    protected string $id;

    public function __construct(
        DataSource\Relation $relation,
        string $id
    ) {
        $this->relation = $relation;
        $this->id = $id;
    }

    public function create(Comparison $comparison, string $operator, Value $value): Bindings
    {
        $alias = Alias::generate('search');
        $id = Identifier::quote_qualified((string)$alias, $this->id);

        // Make NULL values appear as ''
        if ($operator === Operators::IS_EMPTY) {
            $id = sprintf("COALESCE(%s, '')", $id);
        }

        $bindings = $comparison
            ->with_id($id)
            ->get_query_bindings($operator, $value);

        $bindings->join($this->get_join($alias));

        return $bindings;
    }

    protected function get_join(Alias $alias): string
    {
        return RelationJoin::from_relation($this->relation->with_alias($alias))
            ->get_sql();
    }

}
