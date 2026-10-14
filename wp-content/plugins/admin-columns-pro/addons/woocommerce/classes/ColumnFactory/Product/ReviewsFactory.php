<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Product;

use AC\Formatter\Post\Meta;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\ConditionalFormat;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Metric;

class ReviewsFactory extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;

    private const META_KEY = '_wc_review_count';

    public function get_column_type(): string
    {
        return 'column-wc-product_reviews';
    }

    public function get_label(): string
    {
        return __('Reviews', 'woocommerce');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->add(new Formatter\Product\Reviews());
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new Meta(self::META_KEY));
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta(self::META_KEY);
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Number(self::META_KEY);
    }

    protected function get_conditional_format(Config $config): ?FormattableConfig
    {
        return new FormattableConfig(
            new ConditionalFormat\Formatter\Product\ReviewCount()
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Numeric::for_post(self::META_KEY);
    }

}
