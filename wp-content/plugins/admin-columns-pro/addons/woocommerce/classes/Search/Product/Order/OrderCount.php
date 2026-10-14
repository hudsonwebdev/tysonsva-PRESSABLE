<?php

declare(strict_types=1);

namespace ACA\WC\Search\Product\Order;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class OrderCount extends Comparison
{
    public function __construct()
    {
        $operators = new Operators([
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::BETWEEN,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = AliasGenerator::generate_from_class(static::class);
        $join_alias = AliasGenerator::generate('product');

        $sub_query = "
            SELECT product_id, count( 1 ) as order_count
            FROM {$wpdb->prefix}wc_order_product_lookup as $alias
            GROUP BY product_id
        ";

        $comparison = ComparisonFactory::create($join_alias . '.order_count', $operator, $value);

        return $bindings->join(
            " INNER JOIN( {$sub_query}) AS {$join_alias} ON {$wpdb->posts}.ID = {$join_alias}.product_id"
        )
                        ->where($comparison());
    }

}
