<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class Taxonomy extends Base
{
    private string $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $sub_query = $wpdb->prepare(
            "
            SELECT *
            FROM (
                SELECT DISTINCT acsort_tr.object_id, acsort_t.slug
                FROM $wpdb->term_taxonomy AS acsort_tt
                INNER JOIN $wpdb->term_relationships acsort_tr
                    ON acsort_tt.term_taxonomy_id = acsort_tr.term_taxonomy_id
                INNER JOIN $wpdb->terms AS acsort_t
                    ON acsort_t.term_id = acsort_tt.term_id
                WHERE taxonomy = %s
                ORDER BY acsort_t.slug
            ) AS acsort_main
            GROUP BY acsort_main.object_id
        ",
            $this->taxonomy
        );

        $bindings->join(
            Join::left_subquery($sub_query, $alias)
                ->on("$wpdb->posts.ID", '=', "$alias.object_id")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.slug", (string)$order)
        );
    }

}
