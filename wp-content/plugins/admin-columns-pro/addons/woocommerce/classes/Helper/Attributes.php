<?php

declare(strict_types=1);

namespace ACA\WC\Helper;

use AC\Registerable;

/**
 * Cache for the labels of custom (non taxonomy) product attributes. WooCommerce has no registry
 * for these, so they have to be derived from the `_product_attributes` meta of every product.
 * The derived labels are cached, and the query behind them reads the meta in chunks to keep
 * memory flat on large catalogs.
 */
final class Attributes implements Registerable
{
    private const TRANSIENT_KEY = '_aca_wc_custom_attribute_labels';
    private const TTL_SECONDS = DAY_IN_SECONDS;
    private const CHUNK_SIZE = 5000;

    private static array $custom_labels = [];

    private static array $flushed = [];

    /**
     * WooCommerce writes `_product_attributes` through `update_post_meta()` and
     * `delete_post_meta()`, so these three hooks cover every change to the labels.
     */
    public function register(): void
    {
        add_action('added_post_meta', [$this, 'invalidate_on_meta'], 10, 3);
        add_action('updated_post_meta', [$this, 'invalidate_on_meta'], 10, 3);
        add_action('deleted_post_meta', [$this, 'invalidate_on_meta'], 10, 3);
    }

    /**
     * @param int|array $meta_id
     */
    public function invalidate_on_meta($meta_id, int $object_id, string $meta_key): void
    {
        if ('_product_attributes' === $meta_key) {
            $this->invalidate();
        }
    }

    public function invalidate(): void
    {
        $blog_id = get_current_blog_id();

        unset(self::$custom_labels[$blog_id]);

        // Deleting an already deleted transient is a no-op, but a bulk import would still
        // pay for it on every product. The guard is lifted whenever the labels are stored again.
        if (isset(self::$flushed[$blog_id])) {
            return;
        }

        self::$flushed[$blog_id] = true;

        delete_transient(self::TRANSIENT_KEY);
    }

    /**
     * @return array<string, string> Attribute key to label
     */
    public function get_custom_labels(): array
    {
        $blog_id = get_current_blog_id();

        if (isset(self::$custom_labels[$blog_id])) {
            return self::$custom_labels[$blog_id];
        }

        $labels = get_transient(self::TRANSIENT_KEY);

        if (! is_array($labels)) {
            $labels = $this->query_custom_labels();

            set_transient(self::TRANSIENT_KEY, $labels, self::TTL_SECONDS);

            unset(self::$flushed[$blog_id]);
        }

        self::$custom_labels[$blog_id] = $labels;

        return $labels;
    }

    /**
     * Paging on `meta_id` instead of an offset. The `meta_key` index carries the primary key,
     * so every chunk stays an index range scan, and rows can not shift between chunks.
     */
    private function query_custom_labels(): array
    {
        global $wpdb;

        $labels = [];
        $last_id = 0;

        do {
            $rows = (array)$wpdb->get_results(
                $wpdb->prepare(
                    "
                    SELECT meta_id, meta_value 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_product_attributes' AND meta_id > %d 
                    ORDER BY meta_id 
                    LIMIT %d
				",
                    $last_id,
                    self::CHUNK_SIZE
                )
            );

            foreach ($rows as $row) {
                $last_id = (int)$row->meta_id;

                $this->collect_labels((string)$row->meta_value, $labels);
            }
        } while (count($rows) === self::CHUNK_SIZE);

        return $labels;
    }

    private function collect_labels(string $serialized, array &$labels): void
    {
        if (! is_serialized($serialized)) {
            return;
        }

        $attributes = unserialize($serialized, ['allowed_classes' => false]);

        if (! is_array($attributes)) {
            return;
        }

        foreach ($attributes as $key => $attribute) {
            if (! is_array($attribute) || ! empty($attribute['is_taxonomy'])) {
                continue;
            }

            $name = $attribute['name'] ?? null;

            if ($name) {
                $labels[(string)$key] = (string)$name;
            }
        }
    }

}
