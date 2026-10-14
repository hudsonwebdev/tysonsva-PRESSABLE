<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopOrder;

use AC\FormatterCollection;
use AC\MetaType;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Search;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Metric;

class Currency extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;
    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function get_column_type(): string
    {
        return 'column-wc-order_currency';
    }

    public function get_label(): string
    {
        return __('Currency', 'codepress-admin-columns');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->add(new Formatter\Order\Currency());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\ShopOrder\Currency();
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta('_order_currency');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Choice::from_choices(
            MetaType::create_post_meta(),
            '_order_currency',
            get_woocommerce_currencies()
        );
    }

}
