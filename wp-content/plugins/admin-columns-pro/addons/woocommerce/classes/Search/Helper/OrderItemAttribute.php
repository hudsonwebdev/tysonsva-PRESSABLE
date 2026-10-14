<?php

declare(strict_types=1);

namespace ACA\WC\Search\Helper;

use ACP\Sql\AliasGenerator;
use WP_Term;

/**
 * SQL fragments to resolve the attribute value of the variation on an order line.
 *
 * A variable product carries every term of an attribute, so matching an order line on its parent
 * product returns orders for variations that were never purchased. The purchased value is stored
 * on the order line as `pa_x` (or `attribute_pa_x` on older orders) and on the variation itself as
 * `attribute_pa_x`.
 */
final class OrderItemAttribute
{
    private string $taxonomy;

    private string $alias_item_attribute;

    private string $alias_variation;

    private string $alias_variation_attribute;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
        $this->alias_item_attribute = AliasGenerator::generate('item_attribute');
        $this->alias_variation = AliasGenerator::generate('item_variation');
        $this->alias_variation_attribute = AliasGenerator::generate('variation_attribute');
    }

    public static function is_attribute_taxonomy(string $taxonomy): bool
    {
        return in_array($taxonomy, wc_get_attribute_taxonomy_names(), true);
    }

    public function get_joins(string $items_alias): string
    {
        global $wpdb;

        return $wpdb->prepare(
            "
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS {$this->alias_item_attribute}
                ON {$items_alias}.order_item_id = {$this->alias_item_attribute}.order_item_id
                AND {$this->alias_item_attribute}.meta_key IN ( %s, %s )
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS {$this->alias_variation}
                ON {$items_alias}.order_item_id = {$this->alias_variation}.order_item_id
                AND {$this->alias_variation}.meta_key = '_variation_id'
            LEFT JOIN {$wpdb->postmeta} AS {$this->alias_variation_attribute}
                ON {$this->alias_variation_attribute}.post_id = CAST({$this->alias_variation}.meta_value AS UNSIGNED)
                AND {$this->alias_variation_attribute}.meta_key = %s
            ",
            $this->taxonomy,
            'attribute_' . $this->taxonomy,
            'attribute_' . $this->taxonomy
        );
    }

    private function get_selected_value(): string
    {
        return sprintf(
            "COALESCE( NULLIF( %s.meta_value, '' ), NULLIF( %s.meta_value, '' ) )",
            $this->alias_item_attribute,
            $this->alias_variation_attribute
        );
    }

    public function get_matches_term(WP_Term $term): string
    {
        global $wpdb;

        return $wpdb->prepare(
            $this->get_selected_value() . ' IN ( %s, %s )',
            $term->slug,
            $term->name
        );
    }

    /**
     * True for lines without a variation and for variations that accept any value, in which case
     * the terms of the parent product apply.
     */
    public function get_has_no_value(): string
    {
        return $this->get_selected_value() . ' IS NULL';
    }

}
