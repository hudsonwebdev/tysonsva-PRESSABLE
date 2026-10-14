<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Taxonomy;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class PostCount extends Metric\Config\Number
{
    private string $taxonomy;

    private ?array $post_types;

    private ?array $post_status;

    public function __construct(string $taxonomy, ?array $post_types = null, ?array $post_status = null)
    {
        $this->taxonomy = $taxonomy;
        $this->post_types = $post_types;
        $this->post_status = $post_status;
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::for($wpdb->term_taxonomy, 'tt')
            ->select('tt.term_id, COUNT(acm_p.ID) AS acmetric')
            ->join($wpdb->term_relationships, 'acm_tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id', 'acm_tr')
            ->join($wpdb->posts, 'acm_p.ID', '=', 'acm_tr.object_id', 'acm_p')
            ->where('tt.taxonomy', '=', $this->taxonomy)
            ->group_by('tt.term_id');

        if ($this->post_types) {
            $sub_query->and_where_in('acm_p.post_type', $this->post_types);
        }

        if ($this->post_status) {
            $sub_query->and_where_in('acm_p.post_status', $this->post_status);
        }

        $bindings->join(
            Join::inner_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->terms . '.term_id', '=', $alias . '.term_id')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return $alias . '.acmetric';
    }

}
