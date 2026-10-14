<?php

declare(strict_types=1);

namespace ACP\Metric\Config\User;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Expression\Conditional\Coalesce;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class PostCount extends Metric\Config\Number
{
    private ?array $post_types;

    private ?array $post_status;

    public function __construct(?array $post_types, ?array $post_status)
    {
        $this->post_types = $post_types;
        $this->post_status = $post_status;
    }

    protected function configure_query_bindings(AggregationType $aggregation_type, Bindings $bindings, string $alias): void
    {
        global $wpdb;

        $sub_query = QueryBuilder::for($wpdb->users, 'u')
            ->select('u.ID, COUNT(p.ID) AS postcount')
            ->left_join($wpdb->posts, 'p.post_author', '=', 'u.ID', 'p')
            ->group_by('u.ID');

        if ($this->post_status) {
            $sub_query->and_where_in('p.post_status', $this->post_status);
        }

        if ($this->post_types) {
            $sub_query->and_where_in('p.post_type', $this->post_types);
        }

        $bindings->join(
            Join::left_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->users . '.ID', '=', $alias . '.ID')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Coalesce(new Cast($alias . '.postcount', 'DECIMAL(10,2)'), '0');
    }

}
