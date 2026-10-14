<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\Product;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Type\Value;
use DateTime;

class ProductsSold implements Formatter
{
    private array $statuses;

    private ?int $number_of_days;

    public function __construct(array $statuses = ['wc-completed'], ?int $number_of_days = null)
    {
        $this->statuses = $statuses;
        $this->number_of_days = $number_of_days;
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

        $status = (array)apply_filters('ac/wc/column/product/sales/statuses', $this->statuses);
        $status_statement = '';

        if (! empty($status)) {
            $status_statement = sprintf(
                "AND COALESCE( wco_parent.status, wco.status ) IN ('%s')",
                implode("','", array_map('esc_sql', $status))
            );
        }

        $date_statement = '';

        if ($this->number_of_days !== null) {
            $start_date = new DateTime();
            $start_date->modify('-' . $this->number_of_days . ' days');

            $date_statement = sprintf(
                "AND COALESCE( wco_parent.date_created_gmt, wco.date_created_gmt ) >= '%s'",
                $start_date->format('Y-m-d')
            );
        }

        // A refund has its own record with a negative quantity and a status that is always 'wc-completed'.
        // Status and date are therefore taken from the order the refund belongs to.
        $sql = $wpdb->prepare(
            "
            SELECT SUM( wcopl.product_qty )
            FROM {$wpdb->prefix}wc_order_product_lookup AS wcopl
            INNER JOIN {$wpdb->prefix}wc_orders AS wco
                ON wcopl.order_id = wco.id
            LEFT JOIN {$wpdb->prefix}wc_orders AS wco_parent
                ON wco_parent.id = wco.parent_order_id AND wco.type = 'shop_order_refund'
            WHERE ( wcopl.product_id = %d OR wcopl.variation_id = %d )
                AND ( wco.type != 'shop_order_refund' OR ( wco_parent.id IS NOT NULL AND wco.status != 'trash' ) )
                {$status_statement}
                {$date_statement}
        ",
            $product_id,
            $product_id
        );

        return $wpdb->get_var($sql);
    }
}
