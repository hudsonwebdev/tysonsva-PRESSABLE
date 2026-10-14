<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta\User;

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

        $postmeta_alias = AliasGenerator::generate('sorting_postmeta');
        $user_alias = AliasGenerator::generate('sorting_user');

        $bindings->join(
            Join::left($wpdb->postmeta, $postmeta_alias)
                ->on("$wpdb->posts.ID", '=', "$postmeta_alias.post_id")
                ->on("$postmeta_alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->users, $user_alias)
                ->on("$user_alias.ID", '=', "$postmeta_alias.meta_value")
                ->get_sql()
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$user_alias.$this->field", (string)$order)
        );

        return $bindings;
    }

}
