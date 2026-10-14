<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Ratings extends Base
{
    private string $sort_type;

    public function __construct(?string $sort_type = null)
    {
        $this->sort_type = $sort_type ?? 'COUNT'; // 'AVG' or 'COUNT'
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $computation = 'AVG' === $this->sort_type
            ? new ComputationType(ComputationType::AVG)
            : new ComputationType(ComputationType::COUNT);

        $bindings->join(
            Join::left($wpdb->comments, 'acsort_comments')
                ->on('acsort_comments.user_id', '=', "$wpdb->users.ID")
                ->on("acsort_comments.comment_approved", '=', "'1'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->commentmeta, $alias)
                ->on("$alias.comment_id", '=', 'acsort_comments.comment_ID')
                ->on("$alias.meta_key", '=', "'rating'")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->posts, 'acsort_posts')
                ->on('acsort_posts.ID', '=', 'acsort_comments.comment_post_ID')
                ->on("acsort_posts.post_type", '=', "'product'")
                ->get_sql()
        );

        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                $computation,
                "$alias.meta_value",
                (string)$order
            )
        );
    }

}
