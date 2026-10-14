<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;
use WP_Comment_Query;

class Comment extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        add_action('pre_get_comments', [$this, 'modify_comments_query'], 20);
        add_filter('comments_clauses', function (array $clauses) use ($bindings) {
            $this->calculate_metric($clauses, $bindings);
            exit;
        }, 200);
    }

    public function modify_comments_query(WP_Comment_Query $query): void
    {
        $query->query_vars['fields'] = 'ids';
        $query->query_vars['number'] = 0;
        $query->query_vars['orderby'] = 'none';
    }

    protected function calculate_metric(array $clauses, Bindings $bindings): void
    {
        global $wpdb;

        $inner_query = QueryBuilder::create()
            ->select($clauses['fields'])
            ->from($wpdb->comments);

        if ($clauses['join']) {
            $inner_query->join_raw($clauses['join']);
        }

        if ($clauses['where']) {
            $inner_query->where_raw($clauses['where']);
        }

        $outer_query = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($inner_query->get_sql(), 'inner_query')
            ->join_raw(sprintf('INNER JOIN %s ON inner_query.comment_ID = %s.comment_ID', $wpdb->comments, $wpdb->comments));

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
