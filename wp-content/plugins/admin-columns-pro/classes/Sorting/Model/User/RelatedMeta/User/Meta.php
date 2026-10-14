<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User\RelatedMeta\User;

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

        $alias_usermeta_1 = AliasGenerator::generate('sorting_user_meta');
        $alias_usermeta_2 = AliasGenerator::generate('sorting_user_meta');
        $alias_users = AliasGenerator::generate('sorting_users');

        $bindings->join(
            Join::left($wpdb->usermeta, $alias_usermeta_1)
                ->on("$alias_usermeta_1.user_id", '=', "$wpdb->users.ID")
                ->on("$alias_usermeta_1.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
            . "\n" .
            Join::left($wpdb->users, $alias_users)
                ->on("$alias_users.ID", '=', "$alias_usermeta_1.meta_value")
                ->get_sql()
            . "\n" .
            Join::left($wpdb->usermeta, $alias_usermeta_2)
                ->on("$alias_usermeta_2.user_id", '=', "$alias_users.ID")
                ->on("$alias_usermeta_2.meta_key", '=', $wpdb->prepare('%s', $this->meta_field))
                ->get_sql()
        );

        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias_usermeta_2.meta_value", (string)$order)
        );

        return $bindings;
    }

}
