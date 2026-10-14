<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Join;

class Price extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_numeric();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new WcPriceFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left("{$wpdb->prefix}wc_product_meta_lookup", $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.product_id")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Cast("{$alias}.min_price", 'DECIMAL(10,2)');
    }

}
