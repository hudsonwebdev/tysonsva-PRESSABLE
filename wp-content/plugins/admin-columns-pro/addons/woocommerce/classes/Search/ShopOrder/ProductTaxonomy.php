<?php

declare(strict_types=1);

namespace ACA\WC\Search\ShopOrder;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Search\Helper\OrderItemAttribute;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;
use WP_Term;

class ProductTaxonomy extends Comparison implements Comparison\SearchableValues
{
    private string $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;

        $operators = new Operators(
            [
                Operators::EQ,
            ]
        );

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new Bindings())->where(
            $this->get_where((int)$value->get_value())
        );
    }

    public function get_where(int $term_id): string
    {
        global $wpdb;

        $orders = $this->get_orders_ids_by_product_cat($term_id);

        if (empty($orders)) {
            $orders = [0];
        }

        return sprintf("{$wpdb->posts}.ID IN( %s )", implode(',', array_map('intval', $orders)));
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term instanceof WP_Term
            ? (new TermName())->format_label($term)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ]);
    }

    /**
     * Get All orders IDs for a given product ID.
     */
    protected function get_orders_ids_by_product_cat(int $term_id): array
    {
        global $wpdb;

        $ids = get_posts([
            'post_type'      => 'product',
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => $this->taxonomy,
                    'terms'    => $term_id,
                ],
            ],
        ]);

        $product_ids = implode(',', array_map('intval', $ids ?: [0]));

        $term = get_term($term_id, $this->taxonomy);

        $alias_items = AliasGenerator::generate('product_taxonomy_items');
        $alias_product = AliasGenerator::generate('product_taxonomy_product');

        $joins = '';
        $condition = "$alias_product.meta_value IN( $product_ids )";

        // A variable product carries every term of an attribute, so only the value stored on the
        // order line tells us which variation was purchased.
        if ($term instanceof WP_Term && OrderItemAttribute::is_attribute_taxonomy($this->taxonomy)) {
            $attribute = new OrderItemAttribute($this->taxonomy);

            $joins = $attribute->get_joins($alias_items);
            $condition = sprintf(
                '%s OR ( %s AND %s )',
                $attribute->get_matches_term($term),
                $attribute->get_has_no_value(),
                $condition
            );
        }

        $sql = "
	        SELECT $alias_items.order_id
	        FROM {$wpdb->prefix}woocommerce_order_items AS $alias_items
	        INNER JOIN {$wpdb->posts} AS posts ON $alias_items.order_id = posts.ID AND posts.post_type = 'shop_order'
	        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias_product
	            ON $alias_items.order_item_id = $alias_product.order_item_id
	            AND $alias_product.meta_key = '_product_id'
	        $joins
	        WHERE $alias_items.order_item_type = 'line_item'
	        AND ( $condition )
        ";

        return $wpdb->get_col($sql);
    }

}
