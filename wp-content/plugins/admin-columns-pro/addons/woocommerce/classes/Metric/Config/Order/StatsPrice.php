<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP;
use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class StatsPrice extends Metric\Config\Base
{
    private string $column_name;

    public function __construct(string $column_name)
    {
        $this->column_name = $column_name;
    }

    protected function get_column(string $alias): string
    {
        return (string)new ACP\Sql\Expression\Cast(
            sprintf('%s.%s', $alias, $this->column_name),
            'DECIMAL(10,2)'
        );
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->prefix . 'wc_order_stats', $alias)
                ->on($alias . '.order_id', '=', $wpdb->prefix . 'wc_orders.id')
                ->get_sql()
        );
    }

    public function get_aggregation_types(): Metric\AggregationTypes
    {
        return Metric\AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

}
