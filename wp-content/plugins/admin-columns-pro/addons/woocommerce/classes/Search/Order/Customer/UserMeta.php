<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order\Customer;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class UserMeta extends ACP\Search\Comparison
{
    /**
     * @var string
     */
    private string $meta_key;

    public function __construct(string $meta_key)
    {
        parent::__construct(
            new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
                Operators::EQ,
                Operators::NEQ,
            ], false)
        );
        $this->meta_key = $meta_key;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new ACP\Query\Bindings();

        $where = ComparisonFactory::create(
            "um.meta_value",
            $operator,
            $value
        );

        $subquery = $wpdb->prepare(
            "
            SELECT u.ID FROM $wpdb->users as u
                JOIN $wpdb->usermeta as um on u.ID = um.user_id AND um.meta_key = %s AND {$where()}
        ",
            $this->meta_key
        );

        $alias = AliasGenerator::generate_from_class(static::class);
        $order_table = $wpdb->prefix . 'wc_orders';
        $bindings->join("JOIN($subquery) AS $alias on $order_table.customer_id = $alias.ID");

        return $bindings;
    }

}
