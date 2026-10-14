<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Config\Product;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class Featured extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return new AggregationTypes([
            new AggregationType(AggregationType::COUNT, 'yes', __('Featured', 'codepress-admin-columns')),
            new AggregationType(AggregationType::COUNT, 'no', __('Not featured', 'codepress-admin-columns')),
            new AggregationType(AggregationType::COUNT_PERCENTAGE, 'yes', __('Featured', 'codepress-admin-columns')),
            new AggregationType(AggregationType::COUNT_PERCENTAGE, 'no', __('Not featured', 'codepress-admin-columns')),
        ]);
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type, 0);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $term_ids = wc_get_product_visibility_term_ids();

        $bindings->join(
            Join::left($wpdb->term_relationships, $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.object_id")
                ->on("{$alias}.term_taxonomy_id", '=', $wpdb->prepare('%d', $term_ids['featured'] ?? 0))
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf("IF(%s.term_taxonomy_id IS NOT NULL, 'yes', 'no')", $alias);
    }

}
