<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order\Product;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class PostField extends ACP\Search\Comparison
{
    private string $field;

    public function __construct(string $field, Operators $operators, ?string $value_type = null)
    {
        parent::__construct($operators, $value_type);

        $this->field = $field;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return $this->is_analytics_enabled()
            ? $this->create_analytics_bindings($operator, $value)
            : $this->create_non_analytics_bindings($operator, $value);
    }

    private function create_analytics_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $lookup = AliasGenerator::generate('opl');
        $posts = AliasGenerator::generate('p');
        $join_alias = AliasGenerator::generate_from_class(static::class);

        $where = ComparisonFactory::create("$posts.$this->field", $operator, $value)->prepare();

        $subquery = "SELECT DISTINCT $lookup.order_id
            FROM {$wpdb->prefix}wc_order_product_lookup AS $lookup
            INNER JOIN $wpdb->posts AS $posts
                ON $posts.ID = $lookup.product_id OR $posts.ID = $lookup.variation_id
            WHERE $where";

        $bindings = new Bindings();
        $bindings->join(
            "INNER JOIN ($subquery) AS $join_alias ON $join_alias.order_id = {$wpdb->prefix}wc_orders.id"
        );

        return $bindings;
    }

    private function create_non_analytics_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $posts = AliasGenerator::generate('p');
        $items = AliasGenerator::generate('oi');
        $items_meta = AliasGenerator::generate('oim');

        $where = ComparisonFactory::create("$posts.$this->field", $operator, $value)->prepare();

        $product_ids = "SELECT $posts.ID
            FROM $wpdb->posts AS $posts
            WHERE $posts.post_type IN ('product','product_variation')
              AND $where";

        $order_ids = "SELECT DISTINCT $items.order_id
            FROM {$wpdb->prefix}woocommerce_order_items AS $items
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $items_meta
                ON $items.order_item_id = $items_meta.order_item_id
            WHERE $items.order_item_type = 'line_item'
              AND $items_meta.meta_key IN ('_product_id','_variation_id')
              AND $items_meta.meta_value IN ($product_ids)";

        $bindings = new Bindings();
        $bindings->where("{$wpdb->prefix}wc_orders.id IN ($order_ids)");

        return $bindings;
    }

    private function is_analytics_enabled(): bool
    {
        return get_option('woocommerce_analytics_enabled') !== 'no';
    }

}
