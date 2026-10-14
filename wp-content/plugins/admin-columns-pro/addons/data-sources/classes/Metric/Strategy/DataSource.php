<?php

declare(strict_types=1);

namespace ACA\DataSources\Metric\Strategy;

use ACP\Metric\Strategy;
use ACP\Query\Bindings;
use ACP\Sql\QueryBuilder;

class DataSource extends Strategy
{
    public function handle(Bindings $bindings): void
    {
        add_filter(
            'acp/data-sources/pre_get_items',
            function (QueryBuilder $query) use ($bindings) {
                $this->calculate_metric($query, $bindings);
                // Request terminates; metric response has already been sent.
                exit;
            },
            100
        );
    }

    private function calculate_metric(QueryBuilder $query, Bindings $bindings): void
    {
        global $wpdb;

        $inner_query = (clone $query)
            ->order_by()
            ->limit();

        if ($bindings->get_join()) {
            $inner_query->join_raw($bindings->get_join());
        }

        if ($bindings->get_where()) {
            $inner_query->where_raw($bindings->get_where());
        }

        $inner_sql = $inner_query->get_sql();

        $outer = QueryBuilder::create()
            ->select($bindings->get_fields())
            ->from_subquery($inner_sql, 'inner_query');

        $this->send_response((string)$wpdb->get_var($outer->get_sql()));
    }

}
