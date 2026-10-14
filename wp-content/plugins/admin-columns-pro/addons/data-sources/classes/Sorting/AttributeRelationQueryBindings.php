<?php

declare(strict_types=1);

namespace ACA\DataSources\Sorting;

use ACA\DataSources\DataSource;
use ACA\DataSources\Sql\RelationJoin;
use ACP\Query\Bindings;
use ACP\Sorting\Type\Order;
use ACP\Sql\Alias;
use ACP\Sql\Identifier;

class AttributeRelationQueryBindings extends RelationQueryBindings
{
    private string $attribute;

    public function __construct(
        QueryBindings $bindings,
        string $column,
        DataSource\Relation\AttributeRelation $relation,
        string $attribute
    ) {
        parent::__construct($bindings, $column, $relation);

        $this->attribute = $attribute;
    }

    protected function create_relation_query_bindings(Alias $alias, Order $order): Bindings
    {
        global $wpdb;

        $bindings = $this->bindings
            ->with_field($alias . '.' . $this->column)
            ->create_query_bindings($order);

        $join = RelationJoin::from_relation($this->relation->with_alias($alias));

        $join->on(
            Identifier::quote_qualified((string)$alias, $this->relation->get_foreign_attribute()),
            '=',
            $wpdb->prepare('%s', $this->attribute)
        );

        $bindings->join($join->get_sql());

        return $bindings;
    }

}
