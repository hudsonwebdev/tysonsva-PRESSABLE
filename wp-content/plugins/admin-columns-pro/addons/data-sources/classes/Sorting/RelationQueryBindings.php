<?php

declare(strict_types=1);

namespace ACA\DataSources\Sorting;

use ACA\DataSources\DataSource;
use ACA\DataSources\Sql\RelationJoin;
use ACP\Query\Bindings;
use ACP\Sorting;
use ACP\Sorting\Type\Order;
use ACP\Sql\Alias;
use ACP\Sql\Identifier;

class RelationQueryBindings implements Sorting\Model\QueryBindings
{
    protected QueryBindings $bindings;

    protected string $column;

    protected DataSource\Relation $relation;

    public function __construct(
        QueryBindings $bindings,
        string $column,
        DataSource\Relation $relation
    ) {
        $this->bindings = $bindings;
        $this->column = $column;
        $this->relation = $relation;
    }

    protected function create_relation_query_bindings(Alias $alias, Order $order): Bindings
    {
        $bindings = $this->bindings
            ->with_field(Identifier::quote_qualified((string)$alias, $this->column))
            ->create_query_bindings($order);

        $join = RelationJoin::from_relation($this->relation->with_alias($alias));

        $bindings->join($join->get_sql());

        return $bindings;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return $this->create_relation_query_bindings(
            Alias::generate('sorting'),
            $order
        );
    }

}
