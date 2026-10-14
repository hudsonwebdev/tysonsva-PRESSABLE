<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Metric\Config\Order\TotalPaid;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Metric;

class PaidAmountFactory extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Paid Amount', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-order_paid_amount';
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->prepend(new Formatter\Order\PaidAmount());
    }

    protected function get_conditional_format(Config $config): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new TotalPaid();
    }

}
