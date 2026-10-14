<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Sorting;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Metric;

class RefundFactory extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Refunds', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-order_refunds';
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\Order\OrderData('currency');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->prepend(new Formatter\Order\Refunds());
    }

    protected function get_conditional_format(Config $config): ?ACP\ConditionalFormat\FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(new PriceFormatter());
    }

    protected function get_metric(Config $config): Metric\Config
    {
        return new WC\Metric\Config\Order\RefundAmount();
    }

}
