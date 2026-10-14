<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Order;

use AC;
use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Helper;
use AC\Type\TaxonomySlug;
use AC\Type\Value;
use WC_Order_Item_Product;
use WC_Product_Variation;
use WP_Term;

class ProductTerms implements Formatter
{
    private TaxonomySlug $taxonomy;

    public function __construct(TaxonomySlug $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    private function get_items(int $order_id): array
    {
        $order = wc_get_order($order_id);

        if (! $order) {
            return [];
        }

        $items = [];

        foreach ($order->get_items() as $item) {
            if ($item instanceof WC_Order_Item_Product && $item->get_quantity() > 0) {
                $items[] = $item;
            }
        }

        return $items;
    }

    private function get_product_terms(int $product_id): array
    {
        $terms = get_the_terms($product_id, (string)$this->taxonomy);

        return $terms && ! is_wp_error($terms)
            ? $terms
            : [];
    }

    private function is_attribute_taxonomy(): bool
    {
        return in_array((string)$this->taxonomy, wc_get_attribute_taxonomy_names(), true);
    }

    /**
     * The value the customer selected for this attribute, stored on the order item as `pa_x`
     * (or `attribute_pa_x` on older orders). Empty when the variation accepts "Any" value.
     */
    private function get_selected_attribute_slug(WC_Order_Item_Product $item): ?string
    {
        $taxonomy = (string)$this->taxonomy;

        foreach ($item->get_meta_data() as $meta) {
            $key = str_replace('attribute_', '', rawurldecode((string)$meta->key));

            if ($key === $taxonomy && is_scalar($meta->value) && '' !== (string)$meta->value) {
                return (string)$meta->value;
            }
        }

        $variation = wc_get_product($item->get_variation_id());

        if ($variation instanceof WC_Product_Variation) {
            $attributes = $variation->get_attributes();

            if (! empty($attributes[$taxonomy])) {
                return (string)$attributes[$taxonomy];
            }
        }

        return null;
    }

    private function get_attribute_term(string $slug): ?WP_Term
    {
        $taxonomy = (string)$this->taxonomy;

        $term = get_term_by('slug', $slug, $taxonomy) ?: get_term_by('name', $slug, $taxonomy);

        return $term instanceof WP_Term ? $term : null;
    }

    /**
     * A variable product carries every term of an attribute, while the purchased variation only
     * uses one of them. Resolve the variation's own value instead of the parent's full set.
     */
    private function get_item_terms(WC_Order_Item_Product $item): array
    {
        if ($item->get_variation_id() && $this->is_attribute_taxonomy()) {
            $slug = $this->get_selected_attribute_slug($item);

            if (null !== $slug) {
                $term = $this->get_attribute_term($slug);

                return $term ? [$term] : [];
            }
        }

        return $this->get_product_terms($item->get_product_id());
    }

    public function format(Value $value)
    {
        $terms = [];

        foreach ($this->get_items($value->get_id()) as $item) {
            foreach ($this->get_item_terms($item) as $term) {
                // add term_id as key to prevent duplicate entries
                $terms[$term->term_id] = $term;
            }
        }

        uasort($terms, static fn ($a, $b) => strnatcmp($a->name, $b->name));

        if (! $terms) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        $wc_attribute_taxonomies = wc_get_attribute_taxonomy_names();

        $labels = [];

        foreach ($terms as $term) {
            $label = sanitize_term_field('name', $term->name, $term->term_id, $term->taxonomy, 'display');

            // WC taxonomy attributes can not be filtered on the orders list table, link tot taxonomy list table.
            if (in_array($term->taxonomy, $wc_attribute_taxonomies, true)) {
                $url = (string)new AC\Type\Url\ListTable\Taxonomy((string)$this->taxonomy, 'product');
            } else {
                $url = Helper\Taxonomy::create()->get_filter_by_term_url($term, 'product');
            }

            $labels[] = sprintf(
                '<a href="%s">%s</a>',
                $url,
                $label
            );
        }

        return $value->with_value(
            Helper\Strings::create()->enumeration_list($labels, 'and')
        );
    }

}
