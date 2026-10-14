<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class MaxPostDate extends Base
{
    private string $post_type;

    private array $post_stati;

    private bool $oldest_post;

    public function __construct(string $post_type, array $post_stati = [], bool $oldest_post = false)
    {
        $this->post_type = $post_type;
        $this->post_stati = $post_stati;
        $this->oldest_post = $oldest_post;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join = Join::left($wpdb->posts, $alias)
            ->on("$wpdb->users.ID", '=', "$alias.post_author");

        if ($this->post_type !== 'any') {
            $join->on("$alias.post_type", '=', $wpdb->prepare('%s', $this->post_type));
        }

        if ($this->post_stati) {
            $placeholders = implode(', ', array_fill(0, count($this->post_stati), '%s'));
            $join->on("$alias.post_status", 'IN', '(' . $wpdb->prepare($placeholders, $this->post_stati) . ')');
        }

        $bindings->join($join->get_sql());
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType($this->oldest_post ? ComputationType::MIN : ComputationType::MAX),
                "$alias.post_date",
                (string)$order
            )
        );
    }

}
