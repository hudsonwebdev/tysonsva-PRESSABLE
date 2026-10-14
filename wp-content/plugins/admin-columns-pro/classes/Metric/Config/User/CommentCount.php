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

class CommentCount extends Metric\Config\Number
{
    private ?array $status;

    private ?array $post_types;

    public function __construct(?array $status = null, ?array $post_types = null)
    {
        if ($status === null) {
            $status = ['1', '0']; // approved and pending
        }

        $this->status = $status;
        $this->post_types = $post_types;
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb;

        $sub_query = QueryBuilder::for($wpdb->users, 'u')
            ->select('u.ID, COUNT(c.comment_ID) AS commentcount')
            ->left_join($wpdb->comments, 'c.user_id', '=', 'u.ID', 'c')
            ->group_by('u.ID');

        if ($this->post_types) {
            $sub_query->left_join($wpdb->posts . ' as p', 'p.ID', '=', 'c.comment_post_ID');
        }

        if ($this->status) {
            $sub_query->and_where_in('c.comment_approved', $this->status);
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
        return (string)new Coalesce(
            new Cast($alias . '.commentcount', 'DECIMAL(10,2)'),
            '0'
        );
    }

}
