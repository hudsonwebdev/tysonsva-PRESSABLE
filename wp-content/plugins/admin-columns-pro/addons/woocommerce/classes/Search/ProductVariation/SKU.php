<?php

declare(strict_types=1);

namespace ACA\WC\Search\ProductVariation;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;

class SKU extends Comparison
{
    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::CONTAINS,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_products = AliasGenerator::generate_from_class(static::class);
        $alias_product_meta = AliasGenerator::generate('sku');
        $alias_variation_meta = AliasGenerator::generate('sku');

        $join = "
			INNER JOIN {$wpdb->postmeta} AS {$alias_variation_meta} ON {$alias_variation_meta}.post_id = {$wpdb->posts}.ID
			INNER JOIN {$wpdb->posts} AS {$alias_products} ON {$alias_products}.ID = {$wpdb->posts}.post_parent
			INNER JOIN {$wpdb->postmeta} AS {$alias_product_meta} ON {$alias_product_meta}.post_id = {$alias_products}.ID
		";

        $variation_meta_value = $this->get_comparison_meta_value($alias_variation_meta, $operator, $value);
        $product_meta_value = $this->get_comparison_meta_value($alias_product_meta, $operator, $value);

        $where = "
			{$alias_products}.post_type = 'product'
			AND (
		        (
		            {$alias_variation_meta}.meta_key = '_sku' AND
		            {$variation_meta_value}
		        )
		      OR
		        (
		            {$alias_variation_meta}.meta_key = '_sku' AND
		            {$alias_variation_meta}.meta_value = '' AND
		            {$alias_product_meta}.meta_key = '_sku' AND
		            {$product_meta_value}
		        )
			)
		";

        $bindings->join($join)
                 ->where($where)
                 ->group_by("{$wpdb->posts}.ID");

        return $bindings;
    }

    private function get_comparison_meta_value(string $alias, string $operator, Value $value): string
    {
        $comparison = ComparisonFactory::create($alias . '.meta_value', $operator, $value);

        return $comparison->prepare();
    }

}
