<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;

class FeaturedImage extends Metric\Config\Base
{
    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_existence();
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left($wpdb->postmeta, $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.post_id")
                ->on("{$alias}.meta_key", '=', "'_thumbnail_id'")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return sprintf('%s.meta_value', $alias);
    }

}
