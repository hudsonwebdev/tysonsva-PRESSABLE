<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Product\ShopOrder;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Type\Value;

class ProductsSold implements Formatter
{
    private array $statuses;

    public function __construct(array $statuses = ['wc-completed'])
    {
        $this->statuses = $statuses;
    }

    public function format(Value $value)
    {
        $sum = (int)$this->get_total_sold((int)$value->get_id());

        if ($sum < 1) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        return $value->with_value($sum);
    }

    public function get_total_sold(int $product_id): ?string
    {
        global $wpdb;

        $status = (array)apply_filters('ac/wc/column/order/sales/statuses', $this->statuses);
        $status_in = sprintf(
            "'%s'",
            implode("','", array_map('esc_sql', $status))
        );

        // A refund has its own post with a negative quantity and a status that is always 'wc-completed'.
        // The status is therefore taken from the order the refund belongs to.
        $sql = "
			SELECT
			    SUM( oim_q.meta_value )
			FROM
			    {$wpdb->prefix}woocommerce_order_itemmeta AS oim_pid
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON oim_pid.order_item_id = oi.order_item_id
			INNER JOIN $wpdb->posts AS p ON p.ID = oi.order_id
			LEFT JOIN $wpdb->posts AS p_parent ON p_parent.ID = p.post_parent
				AND p.post_type = 'shop_order_refund'
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim_q ON oim_q.order_item_id = oi.order_item_id
				AND oim_q.meta_key = '_qty'
			WHERE oim_pid.meta_key IN ( '_product_id', '_variation_id' )
	        AND oim_pid.meta_value = %s
	        AND ( p.post_type != 'shop_order_refund' OR ( p_parent.ID IS NOT NULL AND p.post_status != 'trash' ) )
	        AND COALESCE( p_parent.post_status, p.post_status ) IN( $status_in )
	   	";

        return $wpdb->get_var($wpdb->prepare($sql, $product_id));
    }
}
