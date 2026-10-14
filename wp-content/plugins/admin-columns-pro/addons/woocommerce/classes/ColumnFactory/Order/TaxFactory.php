<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Sorting;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Metric;

class TaxFactory extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Tax', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-order_tax';
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\Order\OrderData(
            'tax_amount',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->add(new Formatter\Order\TaxTotals());
    }

    protected function get_metric(Config $config): Metric\Config
    {
        return new WC\Metric\Config\Order\OrderField('tax_amount', new Metric\ColumnType\Numeric());
    }

}
