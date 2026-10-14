<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Product;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Metric\Config\Product\StockValue as StockValueMetric;
use ACA\WC\Sorting;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Metric;

class StockValueFactory extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Stock Value', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-wc-stock_value';
    }

    public function get_description(): ?string
    {
        return __(
            'Total value of current stock, calculated as product price multiplied by stock quantity.',
            'codepress-admin-columns'
        );
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new Formatter\Product\StockValue())
            ->add(new Formatter\WcPrice());
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\Product\StockValue();
    }

    protected function get_conditional_format(Config $config): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new Formatter\Product\StockValue());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new StockValueMetric();
    }

}
