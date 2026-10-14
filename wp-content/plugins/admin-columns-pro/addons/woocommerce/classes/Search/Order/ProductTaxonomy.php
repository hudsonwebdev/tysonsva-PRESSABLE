<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Search\Helper\OrderItemAttribute;
use ACP;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;
use ACP\Sql\AliasGenerator;
use WP_Term;

class ProductTaxonomy extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{
    private string $taxonomy;

    public function __construct(string $taxonomy)
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ]),
            Value::DECIMAL
        );

        $this->taxonomy = $taxonomy;
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

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $term = get_term((int)$value->get_value(), $this->taxonomy);

        if ($term instanceof WP_Term && OrderItemAttribute::is_attribute_taxonomy($this->taxonomy)) {
            return $this->create_attribute_query_bindings($term);
        }

        return $this->create_taxonomy_query_bindings((int)$value->get_value());
    }

    /**
     * Match the value of the purchased variation instead of the terms of its parent product.
     */
    private function create_attribute_query_bindings(WP_Term $term): Bindings
    {
        global $wpdb;

        $bindings = new Bindings\QueryArguments();
        $alias_items = AliasGenerator::generate('product_taxonomy_items');
        $alias_product = AliasGenerator::generate('product_taxonomy_product');
        $alias_termrelation = AliasGenerator::generate('product_taxonomy');
        $alias_termtax = AliasGenerator::generate('product_taxonomy');

        $attribute = new OrderItemAttribute($this->taxonomy);

        $parent_has_term = $wpdb->prepare(
            "
            EXISTS (
                SELECT 1
                FROM {$wpdb->prefix}term_relationships AS $alias_termrelation
                INNER JOIN {$wpdb->prefix}term_taxonomy AS $alias_termtax
                    ON $alias_termrelation.term_taxonomy_id = $alias_termtax.term_taxonomy_id
                WHERE $alias_termrelation.object_id = CAST($alias_product.meta_value AS UNSIGNED)
                AND $alias_termtax.taxonomy = %s
                AND $alias_termtax.term_id = %d
            )
            ",
            $this->taxonomy,
            $term->term_id
        );

        $bindings->where(
            "
            EXISTS (
                SELECT 1
                FROM {$wpdb->prefix}woocommerce_order_items AS $alias_items
                INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias_product
                    ON $alias_items.order_item_id = $alias_product.order_item_id
                    AND $alias_product.meta_key = '_product_id'
                {$attribute->get_joins($alias_items)}
                WHERE $alias_items.order_id = {$wpdb->prefix}wc_orders.id
                AND $alias_items.order_item_type = 'line_item'
                AND (
                    {$attribute->get_matches_term($term)}
                    OR ( {$attribute->get_has_no_value()} AND $parent_has_term )
                )
            )
            "
        );

        return $bindings;
    }

    private function create_taxonomy_query_bindings(int $term_id): Bindings
    {
        global $wpdb;

        $bindings = new Bindings\QueryArguments();
        $alias_items = AliasGenerator::generate_from_class(static::class);
        $alias_itemmeta = AliasGenerator::generate('product_taxonomy');
        $alias_termrelation = AliasGenerator::generate('product_taxonomy');
        $alias_termtax = AliasGenerator::generate('product_taxonomy');

        $bindings->join(
            "
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS $alias_items ON {$wpdb->prefix}wc_orders.id = $alias_items.order_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias_itemmeta ON $alias_items.order_item_id = $alias_itemmeta.order_item_id
            INNER JOIN {$wpdb->prefix}term_relationships AS $alias_termrelation ON CAST($alias_itemmeta.meta_value AS UNSIGNED) = $alias_termrelation.object_id
            INNER JOIN {$wpdb->prefix}term_taxonomy AS $alias_termtax ON $alias_termrelation.term_taxonomy_id = $alias_termtax.term_taxonomy_id
            "
        );
        $bindings->where(
            $wpdb->prepare(
                "
                $alias_itemmeta.meta_key = '_product_id' AND
                $alias_termtax.taxonomy = %s AND
                $alias_termtax.term_id = %d
                ",
                $this->taxonomy,
                $term_id
            )
        );

        return $bindings;
    }

}
