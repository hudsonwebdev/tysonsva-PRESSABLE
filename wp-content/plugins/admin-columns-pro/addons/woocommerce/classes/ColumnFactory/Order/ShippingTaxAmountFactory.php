<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Metric;

class ShippingTaxAmountFactory extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Shipping Tax Amount', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-order_shipping_tax';
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Order\OperationalDataPrice('shipping_tax_amount');
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\Order\OperationalData(
            'shipping_tax_amount',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->prepend(new Formatter\Order\ShippingTax());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new WC\Metric\Config\Order\OperationalData('shipping_tax_amount', new WC\Metric\ColumnType\WcPrice());
    }

}
