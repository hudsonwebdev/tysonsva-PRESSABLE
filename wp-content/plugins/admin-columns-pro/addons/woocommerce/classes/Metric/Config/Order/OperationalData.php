<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class OperationalData extends Metric\Config\Base
{
    private string $column_name;

    private Metric\ColumnType $column_type;

    public function __construct(string $column_name, ?Metric\ColumnType $column_type = null)
    {
        $this->column_name = $column_name;
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

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->prefix . 'wc_order_operational_data', $alias)
                ->on($wpdb->prefix . 'wc_orders.id', '=', $alias . '.order_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $this->column_type->get_column_expression(sprintf('%s.%s', $alias, $this->column_name));
    }

}
