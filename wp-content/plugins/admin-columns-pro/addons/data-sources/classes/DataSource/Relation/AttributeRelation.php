<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\Relation;

use ACA\DataSources\DataSource\Relation;

final class AttributeRelation extends ColumnRelation
{
    private string $foreign_attribute;

    private string $foreign_value_column;

    public function __construct(
        Relation $relation,
        string $label,
        string $foreign_attribute,
        string $foreign_value_column
    ) {
        parent::__construct($relation, $label);

        $this->foreign_attribute = $foreign_attribute;
        $this->foreign_value_column = $foreign_value_column;
    }

    public function get_foreign_attribute(): string
    {
        return $this->foreign_attribute;
    }

    public function get_foreign_value_column(): string
    {
        return $this->foreign_value_column;
    }

}
