<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User\RelatedMeta\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class Field implements QueryBindings
{
    private string $field;

    private string $meta_key;

    public function __construct(string $field, string $meta_key)
    {
        $this->field = $field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_usermeta = AliasGenerator::generate('sorting_usermeta');
        $alias_post = AliasGenerator::generate('sorting_post');

        $bindings->join(
            Join::left($wpdb->usermeta, $alias_usermeta)
                ->on("$alias_usermeta.user_id", '=', "$wpdb->users.ID")
                ->on("$alias_usermeta.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->posts, $alias_post)
                ->on("$alias_post.ID", '=', "$alias_usermeta.meta_value")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias_post.$this->field", (string)$order)
        );

        return $bindings;
    }

}
