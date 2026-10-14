<?php

declare(strict_types=1);

namespace ACA\WC\ListTable;

use AC;
use Automattic;

class Orders implements AC\ListTable
{
    private Automattic\WooCommerce\Internal\Admin\Orders\ListTable $table;

    public function __construct(Automattic\WooCommerce\Internal\Admin\Orders\ListTable $list_table)
    {
        $this->table = $list_table;
    }

    public function render_row($id): string
    {
        ob_start();

        $this->table->single_row(wc_get_order($id));

        return (string)ob_get_clean();
    }

    public function render_cell(string $column_id, $row_id): string
    {
        $order = wc_get_order($row_id);

        if (! $order) {
            return '';
        }

        ob_start();

        $method = 'column_' . $column_id;

        if (method_exists($this->table, $method)) {
            call_user_func([$this->table, $method], $order);
        } else {
            // WC's column_default relies on $this->order_type which is only set via setup(),
            // called on the actual orders page — not in AJAX context. Fire the action directly.
            do_action('woocommerce_shop_order_list_table_custom_column', $column_id, $order);
        }

        return (string)ob_get_clean();
    }

}
