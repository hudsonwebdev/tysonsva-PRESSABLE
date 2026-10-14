<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\AggregationTypes;
use ACP\Metric\Formatter\NumberFormatter;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Taxonomy extends Metric\Config\Base
{
    private string $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function create_formatter(AggregationType $aggregation_type): Metric\Formatter
    {
        return new NumberFormatter($aggregation_type);
    }

    public function get_aggregation_types(): AggregationTypes
    {
        return AggregationTypes::for_existence();
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $bindings->join(
            Join::left_subquery($this->get_sub_query()->get_sql(), $alias)
                ->on($wpdb->posts . '.ID', '=', $alias . '.object_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.term_count';
    }

    private function get_sub_query(): QueryBuilder
    {
        global $wpdb;

        return QueryBuilder::create()
            ->select('tr.object_id, NULLIF(COUNT(DISTINCT term_id), 0) AS term_count')
            ->from($wpdb->term_relationships, 'tr')
            ->join($wpdb->term_taxonomy, 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id', 'tt')
            ->where('tt.taxonomy', '=', $this->taxonomy)
            ->group_by('tr.object_id');
    }

}
