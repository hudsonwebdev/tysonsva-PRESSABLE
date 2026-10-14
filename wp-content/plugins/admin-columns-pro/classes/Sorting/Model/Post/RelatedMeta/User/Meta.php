<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta\User;

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
        $this->meta_key = $meta_key;
        $this->meta_field = $meta_field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $postmeta_alias = AliasGenerator::generate('sorting_postmeta');
        $users_alias = AliasGenerator::generate('sorting_user');
        $usermeta_alias = AliasGenerator::generate('sorting_usermeta');

        $bindings->join(
            Join::left($wpdb->postmeta, $postmeta_alias)
                ->on("$wpdb->posts.ID", '=', "$postmeta_alias.post_id")
                ->on("$postmeta_alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->users, $users_alias)
                ->on("$users_alias.ID", '=', "$postmeta_alias.meta_value")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->usermeta, $usermeta_alias)
                ->on("$usermeta_alias.user_id", '=', "$users_alias.ID")
                ->on("$usermeta_alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_field))
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$usermeta_alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}
