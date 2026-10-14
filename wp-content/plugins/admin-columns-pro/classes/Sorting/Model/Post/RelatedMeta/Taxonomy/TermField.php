<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class TermField implements QueryBindings
{
    private string $term_field;

    private string $meta_key;

    public function __construct(string $term_field, string $meta_key)
    {
        $this->term_field = $term_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $postmeta_alias = AliasGenerator::generate('sorting_postmeta');
        $term_alias = AliasGenerator::generate('sorting_term');

        $bindings->join(
            Join::left($wpdb->postmeta, $postmeta_alias)
                ->on("$wpdb->posts.ID", '=', "$postmeta_alias.post_id")
                ->on("$postmeta_alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->terms, $term_alias)
                ->on("$term_alias.term_id", '=', "$postmeta_alias.meta_value")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$term_alias.$this->term_field",
                (string)$order
            )
        );

        return $bindings;
    }

}
