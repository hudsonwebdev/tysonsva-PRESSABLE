<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class MetaCount extends Base
{
    protected string $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $orderby = SqlOrderByFactory::create_with_computation(
            new ComputationType(ComputationType::COUNT),
            "$alias.meta_key",
            (string)$order,
            true
        );

        $bindings
            ->join(
                Join::left($wpdb->termmeta, $alias)
                    ->on('t.term_id', '=', "$alias.term_id")
                    ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                    ->get_sql()
            )
            ->group_by("t.term_id")
            ->order_by($orderby);
    }

}
