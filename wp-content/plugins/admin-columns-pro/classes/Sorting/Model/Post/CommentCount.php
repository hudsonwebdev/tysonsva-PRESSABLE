<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

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

    private array $stati;

    public function __construct(array $stati = [])
    {
        $this->stati = $stati;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join = Join::left($wpdb->comments, $alias)
            ->on("$alias.comment_post_ID", '=', "$wpdb->posts.ID");

        if ($this->stati) {
            $placeholders = implode(', ', array_fill(0, count($this->stati), '%s'));
            $join->on("$alias.comment_approved", 'IN', '(' . $wpdb->prepare($placeholders, $this->stati) . ')');
        }

        $bindings->join($join->get_sql());
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.comment_ID", (string)$order)
        );
    }

}
