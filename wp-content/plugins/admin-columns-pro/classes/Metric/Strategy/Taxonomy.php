<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;
use WP_Term_Query;

class Taxonomy extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        add_action('pre_get_terms', [$this, 'modify_terms_query'], 20);
        add_filter('terms_pre_query', function ($terms, WP_Term_Query $query) use ($bindings) {
            $this->calculate_metric($query->request, $bindings);
            exit;
        }, 110, 2);
    }

    public function modify_terms_query(WP_Term_Query $query): void
    {
        $query->query_vars['number'] = 0;
        $query->query_vars['orderby'] = 'none';
    }

    private function calculate_metric(string $request, Bindings $bindings): void
    {
        global $wpdb;

        $outer_query = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($request, 'inner_query')
            ->join_raw(sprintf('INNER JOIN %s ON inner_query.term_id = %s.term_id', $wpdb->terms, $wpdb->terms));

        if ($bindings->get_join()) {
            $outer_query->join_raw($bindings->get_join());
        }

        if ($bindings->get_where()) {
            $outer_query->where_raw($bindings->get_where());
        }

        $this->send_response(
            (string)$wpdb->get_var($outer_query->get_sql())
        );
    }

}
