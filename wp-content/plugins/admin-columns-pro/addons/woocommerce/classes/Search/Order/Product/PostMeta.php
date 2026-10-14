<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order\Product;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class PostMeta extends ACP\Search\Comparison
{
    private string $meta_key;

    public function __construct(string $meta_key, Operators $operators, ?string $value_type = null)
    {
        parent::__construct($operators, $value_type);

        $this->meta_key = $meta_key;
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
        $meta = AliasGenerator::generate('pm');
        $join_alias = AliasGenerator::generate_from_class(static::class);

        $where = ComparisonFactory::create("$meta.meta_value", $operator, $value)->prepare();
        $meta_key_clause = $wpdb->prepare("$meta.meta_key = %s", $this->meta_key);

        $subquery = "SELECT DISTINCT $lookup.order_id
            FROM {$wpdb->prefix}wc_order_product_lookup AS $lookup
            INNER JOIN $wpdb->postmeta AS $meta
                ON ($meta.post_id = $lookup.product_id OR $meta.post_id = $lookup.variation_id)
               AND $meta_key_clause
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

        $meta = AliasGenerator::generate('pm');
        $items = AliasGenerator::generate('oi');
        $items_meta = AliasGenerator::generate('oim');

        $where = ComparisonFactory::create("$meta.meta_value", $operator, $value)->prepare();
        $meta_key_clause = $wpdb->prepare("$meta.meta_key = %s", $this->meta_key);

        $product_ids = "SELECT $meta.post_id
            FROM $wpdb->postmeta AS $meta
            WHERE $meta_key_clause AND $where";

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
