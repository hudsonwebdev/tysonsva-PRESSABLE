<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopOrder;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Metric;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Column\AdvancedColumnFactory;
use ACP\ConditionalFormat\ConditionalFormatTrait;

class Subtotal extends AdvancedColumnFactory
{
    use WooCommerceGroupTrait;
    use ConditionalFormatTrait;

    public function get_column_type(): string
    {
        return 'column-wc-order-subtotal';
    }

    public function get_label(): string
    {
        return __('Subtotal', 'woocommerce');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->add(new Formatter\Order\SubTotal());
    }

    protected function get_metric(Config $config): ?ACP\Metric\Config
    {
        return new Metric\Config\ShopOrder\Subtotal();
    }

}
