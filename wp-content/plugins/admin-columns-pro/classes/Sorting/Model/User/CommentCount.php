<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class CommentCount extends Base
{
    public const STATUS_APPROVED = '1';
    public const STATUS_SPAM = 'spam';
    public const STATUS_TRASH = 'trash';
    public const STATUS_PENDING = '0';

    private array $status;

    private array $post_types;

    public function __construct(array $status = [], array $post_types = [])
    {
        if (empty($status)) {
            $status = [self::STATUS_APPROVED, self::STATUS_PENDING];
        }

        $this->status = $status;
        $this->post_types = $post_types;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join = Join::left($wpdb->comments, $alias)
            ->on("$alias.user_id", '=', "$wpdb->users.ID");

        if ($this->status) {
            $placeholders = implode(', ', array_fill(0, count($this->status), '%s'));
            $join->on("$alias.comment_approved", 'IN', '(' . $wpdb->prepare($placeholders, $this->status) . ')');
        }

        $join_sql = $join->get_sql();

        if ($this->post_types) {
            $placeholders = implode(', ', array_fill(0, count($this->post_types), '%s'));
            $join_sql .= "\n" . Join::left($wpdb->posts, 'acsort_posts')
                ->on('acsort_posts.ID', '=', "$alias.comment_post_ID")
                ->on('acsort_posts.post_type', 'IN', '(' . $wpdb->prepare($placeholders, $this->post_types) . ')')
                ->get_sql();
        }

        $bindings->join($join_sql);
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                "$alias.comment_ID",
                (string)$order
            )
        );
    }

}
