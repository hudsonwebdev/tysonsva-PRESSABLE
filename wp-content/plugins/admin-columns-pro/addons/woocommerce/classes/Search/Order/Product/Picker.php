<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order\Product;

use ACA\WC\Helper\Select;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class Picker extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{
    use Select\ProductAndVariationValuesTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = AliasGenerator::generate_from_class(static::class);

        $product_id = (int)$value->get_value();

        $bindings->join(
            "INNER JOIN {$wpdb->prefix}wc_order_product_lookup AS $alias ON {$wpdb->prefix}wc_orders.id {$operator} $alias.order_id"
        );
        $bindings->where(
            $wpdb->prepare("($alias.product_id = %d OR $alias.variation_id = %d)", $product_id, $product_id)
        );

        return $bindings;
    }

}
