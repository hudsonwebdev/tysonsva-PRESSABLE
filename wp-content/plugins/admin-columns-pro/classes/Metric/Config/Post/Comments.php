<?php

declare(strict_types=1);

namespace ACP\Metric\Config\Post;

use ACP\Metric;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\Expression\Cast;
use ACP\Sql\Join;
use ACP\Sql\QueryBuilder;

class Comments extends Metric\Config\Number
{
    private array $statuses;

    public function __construct(array $statuses = [])
    {
        $this->statuses = $statuses;
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
        global $wpdb;

        $sub_query = QueryBuilder::for($wpdb->posts, 'p')
            ->select("p.ID, COUNT(c.comment_ID) AS comment_count")
            ->left_join(
                $wpdb->comments,
                function (Join $join) {
                    $join
                        ->on('p.ID', '=', 'c.comment_post_ID')
                        ->on('c.comment_approved', '=', '1');
                },
                null,
                null,
                'c'
            )
            ->group_by('p.ID');

        if ($this->statuses) {
            $sub_query->where_in('c.comment_approved', $this->statuses);
        }

        $bindings->join(
            Join::inner_subquery($sub_query->get_sql(), $alias)
                ->on($wpdb->posts . '.ID', '=', $alias . '.ID')
                ->get_sql()
        );
    }

    protected function get_column(string $alias): string
    {
        return (string)new Cast($alias . '.comment_count', 'DECIMAL(10,2)');
    }

}
