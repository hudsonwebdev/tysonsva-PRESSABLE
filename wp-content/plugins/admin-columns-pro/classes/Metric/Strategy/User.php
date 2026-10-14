<?php

declare(strict_types=1);

namespace ACP\Metric\Strategy;

use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;
use WP_User_Query;

class User extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        add_action('pre_get_users', [$this, 'modify_users_query'], 120);
        add_action('pre_user_query', function (WP_User_Query $query) use ($bindings) {
            $request = implode(' ', array_filter([
                'SELECT ' . $query->query_fields,
                $query->query_from,
                $query->query_where,
                $query->query_orderby,
                $query->query_limit,
            ]));

            $this->calculate_metric($request, $bindings);
            exit;
        }, 110);
    }

    public function modify_users_query(WP_User_Query $query): void
    {
        $query->set('number', -1);
        $query->set('fields', 'ids');
        $query->set('count_total', false);
        $query->set('orderby', 'none');
    }

    private function calculate_metric(string $request, Bindings $bindings): void
    {
        global $wpdb;

        $outer_query = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($request, 'inner_query')
            ->join_raw(sprintf('INNER JOIN %s ON inner_query.ID = %s.ID', $wpdb->users, $wpdb->users));

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
