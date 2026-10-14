<?php

declare(strict_types=1);

namespace ACA\DataSources\Metric\Config;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\ColumnType;
use ACP\Metric\Type\AggregationType;

class DataSourceField extends Metric\Config\Base
{
    private string $column;

    private ColumnType $column_type;

    public function __construct(string $column, ColumnType $column_type)
    {
        $this->column = $column;
        $this->column_type = $column_type;
    }

    public function get_column_type(): ColumnType
    {
        return $this->column_type;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return $this->column_type->create_formatter($aggregation_type);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->column_type->get_aggregation_types();
    }

    protected function get_column(string $alias): string
    {
        return $this->column_type->get_column_expression($this->column);
    }

}
