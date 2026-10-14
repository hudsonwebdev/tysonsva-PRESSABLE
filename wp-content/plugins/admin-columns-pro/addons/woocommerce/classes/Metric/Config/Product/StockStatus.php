<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class StockStatus extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        $statuses = [
            'instock'     => __('In stock', 'codepress-admin-columns'),
            'outofstock'  => __('Out of stock', 'codepress-admin-columns'),
            'onbackorder' => __('On backorder', 'codepress-admin-columns'),
        ];

        $types = [];

        foreach ($statuses as $status => $label) {
            $types[] = new AggregationType(AggregationType::COUNT, $status, $label);
        }

        foreach ($statuses as $status => $label) {
            $types[] = new AggregationType(AggregationType::COUNT_PERCENTAGE, $status, $label);
        }

        return new AggregationTypes($types);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_product_meta_lookup", $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.product_id")
                ->get_sql()
        );
        $bindings->group_by('NULL');
        $bindings->order_by('NULL');
    }

    protected function get_column(string $alias): string
    {
        return "$alias.stock_status";
    }

}
