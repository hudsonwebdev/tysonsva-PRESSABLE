<?php

declare(strict_types=1);

namespace ACP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\ColumnType\Existence;
use ACP\Metric\Type\AggregationType;

class UserField extends Metric\Config\Base
{
    private string $field;

    private ColumnType $column_type;

    public function __construct(string $field, ?ColumnType $column_type = null)
    {
        $this->field = $field;
        $this->column_type = $column_type ?: new Existence();
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->column_type->get_aggregation_types();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->column_type->create_formatter($aggregation_type);
    }

    protected function get_column(string $alias): string
    {
        global $wpdb;

        return $this->column_type->get_column_expression(
            sprintf('%s.%s', $wpdb->users, $this->field)
        );
    }

}
