<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Conditional\NullIf;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Revisions extends Metric\Config\Number
{
    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left_subquery($this->get_sub_query()->get_sql(), $alias)
                ->on($wpdb->posts . '.ID', '=', $alias . '.post_parent')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new NullIf($alias . '.revision_count', '0');
    }

    private function get_sub_query(): QueryBuilder
    {
        global $wpdb;

        return QueryBuilder::create()
            ->select('post_parent, COUNT(*) as revision_count')
            ->from($wpdb->posts)
            ->where_raw("post_type = 'revision'")
            ->and_where_raw("post_status = 'inherit'")
            ->group_by('post_parent');
    }

}
