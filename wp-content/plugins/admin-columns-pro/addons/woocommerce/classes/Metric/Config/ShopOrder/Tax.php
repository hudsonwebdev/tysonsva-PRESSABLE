<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\ShopOrder;

use ACA\WC\Metric\Formatter\WcPriceFormatter;
use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;

class Tax extends Metric\Config\Base
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
            Join::left($wpdb->postmeta, $alias . '_tax')
                ->on("{$wpdb->posts}.ID", '=', "{$alias}_tax.post_id")
                ->on("{$alias}_tax.meta_key", '=', $wpdb->prepare('%s', '_order_tax'))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias . '_stax')
                ->on("{$wpdb->posts}.ID", '=', "{$alias}_stax.post_id")
                ->on("{$alias}_stax.meta_key", '=', $wpdb->prepare('%s', '_order_shipping_tax'))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf(
            '%s + %s',
            new Coalesce(new Cast("{$alias}_tax.meta_value", 'DECIMAL(10,2)'), '0'),
            new Coalesce(new Cast("{$alias}_stax.meta_value", 'DECIMAL(10,2)'), '0')
        );
    }

}
