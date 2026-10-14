<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Order;

use ACA\WC\Type\AddressType;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class AddressColumn extends Metric\Config\Base
{
    private AddressType $address_type;

    private string $column_name;

    private ?AggregationTypes $metrics;

    public function __construct(AddressType $address_type, string $column_name, ?AggregationTypes $metrics = null)
    {
        $this->address_type = $address_type;
        $this->column_name = $column_name;
        $this->metrics = $metrics;
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $order_table = $wpdb->prefix . 'wc_orders';
        $address_table = $wpdb->prefix . 'wc_order_addresses';

        $bindings->join(
            Join::left($address_table, $alias)
                ->on($alias . '.order_id', '=', $order_table . '.id')
                ->on($alias . '.address_type', '=', $wpdb->prepare('%s', (string)$this->address_type))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf('%s.%s', $alias, $this->column_name);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return $this->metrics ?? AggregationTypes::for_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

}
