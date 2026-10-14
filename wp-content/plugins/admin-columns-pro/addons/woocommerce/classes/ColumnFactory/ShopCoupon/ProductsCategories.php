<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopCoupon;

use AC\Formatter\Term\TermProperty;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Metric;

class ProductsCategories extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;
    use WooCommerceGroupTrait;

    public function get_column_type(): string
    {
        return 'column-wc-coupon_product_categories';
    }

    public function get_label(): string
    {
        return __('Product Categories', 'woocommerce');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new Formatter\ShopCoupon\ProductCategoriesCollection())
            ->add(new TermProperty('name'));
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\ShopCoupon\ProductCategories();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\ShopCoupon\Categories('product_categories');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_post('product_categories');
    }

}
