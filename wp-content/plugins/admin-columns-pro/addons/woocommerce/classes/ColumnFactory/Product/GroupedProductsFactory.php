<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Product;

use AC\Formatter\Post\PostLink;
use AC\Formatter\Post\PostTitle;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Setting\ComponentFactory\PostCollectionDisplay;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;

class GroupedProductsFactory extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
    use WooCommerceGroupTrait;

    private PostCollectionDisplay $display;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        PostCollectionDisplay $display
    ) {
        parent::__construct($feature_setting_builder_factory, $default_settings_builder);

        $this->display = $display;
    }

    public function get_column_type(): string
    {
        return 'column-wc-product-grouped_products';
    }

    public function get_label(): string
    {
        return __('Grouped Products', 'woocommerce');
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return new ComponentCollection([
            $this->display->create($config),
        ]);
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        $formatters = parent::get_formatters($config);
        $formatters->prepend(new Formatter\Product\GroupedProducts());

        $display = (string)$config->get(PostCollectionDisplay::NAME, PostCollectionDisplay::COUNT);

        if ($config->get('s')) {
            if ($display !== PostCollectionDisplay::PROPERTIES) {
                $formatters->add(new PostTitle());
                $formatters->add(new PostLink('edit_post'));
            }

            return $formatters;
        }

        if ($display === PostCollectionDisplay::COUNT) {
            $formatters->add(new Formatter\Product\GroupedProductsExtended());
        }

        return $formatters;
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\Product\GroupedProducts();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Product\GroupedProducts();
    }

}
