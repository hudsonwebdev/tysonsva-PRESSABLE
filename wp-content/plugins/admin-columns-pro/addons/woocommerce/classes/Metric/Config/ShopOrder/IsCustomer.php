<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\ShopOrder;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class IsCustomer extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_choices([
            '0' => __('Guest', 'woocommerce'),
            '1' => __('Customer', 'woocommerce'),
        ]);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->postmeta, $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.post_id")
                ->on("{$alias}.meta_key", '=', $wpdb->prepare('%s', '_customer_user'))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf("CASE WHEN CAST(%s.meta_value AS UNSIGNED) > 0 THEN '1' ELSE '0' END", $alias);
    }

}
