<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class PostCount extends Base
{
    private ?array $post_types;

    private ?array $post_status;

    public function __construct(?array $post_types = null, ?array $post_status = null)
    {
        $this->post_types = $post_types;
        $this->post_status = $post_status;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $join = Join::left($wpdb->posts, $alias)
            ->on("$alias.post_author", '=', "$wpdb->users.ID");

        if ($this->post_status) {
            $placeholders = implode(', ', array_fill(0, count($this->post_status), '%s'));
            $join->on("$alias.post_status", 'IN', '(' . $wpdb->prepare($placeholders, $this->post_status) . ')');
        }

        if ($this->post_types) {
            $placeholders = implode(', ', array_fill(0, count($this->post_types), '%s'));
            $join->on("$alias.post_type", 'IN', '(' . $wpdb->prepare($placeholders, $this->post_types) . ')');
        }

        $bindings->join($join->get_sql());
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::COUNT),
                "$alias.ID",
                (string)$order,
                true
            )
        );
    }

}
