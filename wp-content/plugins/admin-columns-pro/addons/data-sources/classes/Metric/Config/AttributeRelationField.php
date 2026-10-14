<?php

declare(strict_types=1);

namespace ACA\DataSources\Metric\Config;

use ACA\DataSources\DataSource;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Identifier;
use ACP\Sql\Join;

class AttributeRelationField extends Metric\Config\Base
{
    private DataSource\Relation\AttributeRelation $relation;

    private string $attribute;

    private ColumnType $column_type;

    public function __construct(
        DataSource\Relation\AttributeRelation $relation,
        string $attribute,
        ColumnType $column_type
    ) {
        $this->relation = $relation;
        $this->attribute = $attribute;
        $this->column_type = $column_type;
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->column_type->get_aggregation_types();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->column_type->create_formatter($aggregation_type);
    }

    private function requires_left_join(AggregationType $aggregation_type): bool
    {
        return in_array($aggregation_type->get_value(), [
            AggregationType::IS_EMPTY,
            AggregationType::IS_EMPTY_PERCENTAGE,
            AggregationType::IS_NOT_EMPTY_PERCENTAGE,
        ], true);
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb;

        $foreign_table = $this->relation->get_foreign_data_source()->get_repository()->get_table();

        $join = $this->requires_left_join($aggregation_type)
            ? Join::left($foreign_table, $alias)
            : Join::inner($foreign_table, $alias);

        $join
            ->on(
                Identifier::quote_qualified(
                    $this->relation->get_local_table(),
                    $this->relation->get_local_column()
                ),
                '=',
                Identifier::quote_qualified($alias, $this->relation->get_foreign_column())
            )
            ->on(
                Identifier::quote_qualified($alias, $this->relation->get_foreign_attribute()),
                '=',
                $wpdb->prepare('%s', $this->attribute)
            );

        $bindings->join($join->get_sql());
    }

    protected function get_column(string $alias): string
    {
        return $this->column_type->get_column_expression(
            Identifier::quote_qualified($alias, $this->relation->get_foreign_value_column())
        );
    }

}
