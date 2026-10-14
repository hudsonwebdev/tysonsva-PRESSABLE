<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;
use WP_Query;

class Post extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        add_action('pre_get_posts', [$this, 'modify_posts_query'], 20);
        add_filter('posts_request', function ($request, WP_Query $query) use ($bindings) {
            if (! $query->is_main_query()) {
                return $request;
            }

            $this->calculate_metric($request, $bindings);
            exit;
        }, 10, 2);
    }

    public function modify_posts_query(WP_Query $query): void
    {
        if ($query->is_main_query()) {
            $query->set('posts_per_page', -1);
            $query->set('posts_per_archive_page', -1);
            $query->set('fields', 'ids');
        }
    }

    private function calculate_metric(string $request, Bindings $bindings): void
    {
        global $wpdb;

        $request = str_replace('SQL_CALC_FOUND_ROWS ', '', $request);

        $outer_query = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($request, 'inner_query')
            ->join_raw(sprintf('INNER JOIN %s ON inner_query.ID = %s.ID', $wpdb->posts, $wpdb->posts));

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
