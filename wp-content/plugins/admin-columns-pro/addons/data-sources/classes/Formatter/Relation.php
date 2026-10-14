<?php

declare(strict_types=1);

namespace ACA\DataSources\Formatter;

use AC\Setting\Formatter;
use AC\Type\Value;
use AC\Type\ValueCollection;
use ACA\DataSources\DataSource;
use ACA\DataSources\Repository;
use ACA\DataSources\Sql\TableAwareQueryBuilder;
use ACP\Sql\Identifier;

class Relation implements Formatter
{
    protected string $column;

    protected string $id;

    protected DataSource\Relation $relation;

    public function __construct(
        string $id,
        string $column,
        DataSource\Relation $relation
    ) {
        $this->id = $id;
        $this->column = $column;
        $this->relation = $relation;
    }

    protected function get_builder(array $row, Repository $repository): TableAwareQueryBuilder
    {
        return $repository
            ->query()
            ->select(Identifier::quote($this->column))
            ->where(
                $this->relation->get_foreign_column(),
                $row[$this->relation->get_local_column()]
            );
    }

    public function format(Value $value): Value
    {
        $row = $value->get_value();

        if (is_array($row)) {
            $foreign_repository = $this->relation->get_foreign_data_source()->get_repository();
            $builder = $this->get_builder($row, $foreign_repository);

            if ($this->relation->get_cardinality()->is_to_one()) {
                $row[$this->id] = $builder->one()[$this->column] ?? '';
            } elseif ($this->relation->get_cardinality()->is_to_many()) {
                $value_collection = new ValueCollection($value->get_id());

                foreach ($builder->all() as $related_row) {
                    $value_collection->add($value->with_value($related_row[$this->column]));
                }

                $row[$this->id] = $value_collection;
            }

            $value = $value->with_value($row);
        }

        return $value;
    }

}
