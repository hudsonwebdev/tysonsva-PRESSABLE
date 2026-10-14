<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;

class OrderField extends Metric\Config\Base
{
    private string $column;

    private Metric\ColumnType $column_type;

    public function __construct(string $column, ?Metric\ColumnType $column_type = null)
    {
        $this->column = $column;
        $this->column_type = $column_type ?: new Metric\ColumnType\Existence();
    }

    public function get_aggregation_types(): Metric\AggregationTypes
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

        return $this->column_type->get_column_expression($wpdb->prefix . 'wc_orders.' . $this->column);
    }

}
