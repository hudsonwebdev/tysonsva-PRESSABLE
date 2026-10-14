<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Media;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Conditional\NullIf;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class UsedAsFeaturedImage extends Metric\Config\Number
{
    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::create()
            ->select('p2.ID, COUNT(pm.post_id) AS metric_count')
            ->from($wpdb->posts . ' AS p2')
            ->left_join(
                $wpdb->postmeta . ' AS pm',
                function (Join $join) {
                    $join->on('pm.meta_key', '=', "'_thumbnail_id'")
                         ->on('pm.meta_value', '=', 'p2.ID');
                }
            )
            ->where('p2.post_type', '=', 'attachment')
            ->group_by('p2.ID');

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on("{$wpdb->posts}.ID", '=', "{$alias}.ID")
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new NullIf(sprintf('%s.metric_count', $alias), '0');
    }

}
