<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\QueryBindingsFactory;

use ACA\DataSources\DataSource;
use ACA\DataSources\Sql\RelationJoin;
use ACP\Sql\Alias;
use ACP\Sql\Identifier;

class AttributeRelation extends Relation
{
    private string $attribute;

    private DataSource\Relation\AttributeRelation $attribute_relation;

    public function __construct(
        DataSource\Relation\AttributeRelation $relation,
        string $id,
        string $attribute
    ) {
        parent::__construct($relation, $id);

        $this->attribute = $attribute;
        $this->attribute_relation = $relation;
    }

    protected function get_join(Alias $alias): string
    {
        global $wpdb;

        $join = RelationJoin::from_relation($this->relation->with_alias($alias));

        $join->on(
            Identifier::quote_qualified((string)$alias, $this->attribute_relation->get_foreign_attribute()),
            '=',
            $wpdb->prepare('%s', $this->attribute)
        );

        return $join->get_sql();
    }

}
