<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class CouponsUsed extends Comparison
{
    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::CONTAINS,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            Value::STRING
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $in_operator = $operator === Operators::IS_EMPTY ? 'NOT IN' : 'IN';

        return (new Bindings())->where(
            sprintf(
                "{$wpdb->prefix}wc_orders.id %s ( %s )",
                $in_operator,
                $this->get_order_ids_with_coupon($operator, $value)
            )
        );
    }

    private function get_order_ids_with_coupon(string $operator, Value $value): string
    {
        global $wpdb;

        $items = AliasGenerator::generate('oi');

        $where = '';

        if (in_array($operator, [Operators::EQ, Operators::CONTAINS], true)) {
            $where = ' AND ' . ComparisonFactory::create("$items.order_item_name", $operator, $value)->prepare();
        }

        return "SELECT DISTINCT $items.order_id
            FROM {$wpdb->prefix}woocommerce_order_items AS $items
            WHERE $items.order_item_type = 'coupon'" . $where;
    }

}
