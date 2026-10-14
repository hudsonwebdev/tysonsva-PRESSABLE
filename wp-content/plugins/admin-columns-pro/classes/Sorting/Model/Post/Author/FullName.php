<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;
use ACP\Sql\Join;

class FullName implements QueryBindings
{
    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_firstname = AliasGenerator::generate('sorting_author_firstname');
        $alias_lastname = AliasGenerator::generate('sorting_author_lastname');

        $bindings->join(
            Join::inner($wpdb->usermeta, $alias_firstname)
                ->on("$wpdb->posts.post_author", '=', "$alias_firstname.user_id")
                ->on("$alias_firstname.meta_key", '=', "'first_name'")
                ->get_sql()
            . "\n" .
            Join::inner($wpdb->usermeta, $alias_lastname)
                ->on("$wpdb->posts.post_author", '=', "$alias_lastname.user_id")
                ->on("$alias_lastname.meta_key", '=', "'last_name'")
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                [
                    "$alias_firstname.meta_value",
                    "$alias_lastname.meta_value",
                ],
                (string)$order
            )
        );

        return $bindings;
    }

}
