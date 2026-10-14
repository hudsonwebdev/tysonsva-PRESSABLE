<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class Meta implements QueryBindings
{
    private string $meta_field;

    private string $meta_key;

    public function __construct(string $meta_field, string $meta_key)
    {
        $this->meta_field = $meta_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_postmeta1 = AliasGenerator::generate('sorting_postmeta');
        $alias_postmeta2 = AliasGenerator::generate('sorting_postmeta');

        $bindings->join(
            Join::left($wpdb->postmeta, $alias_postmeta1)
                ->on("$wpdb->posts.ID", '=', "$alias_postmeta1.post_id")
                ->on("$alias_postmeta1.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->postmeta, $alias_postmeta2)
                ->on("$alias_postmeta1.meta_value", '=', "$alias_postmeta2.post_id")
                ->on("$alias_postmeta2.meta_key", '=', $wpdb->prepare('%s', $this->meta_field))
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias_postmeta2.meta_value", (string)$order)
        );

        return $bindings;
    }

}
