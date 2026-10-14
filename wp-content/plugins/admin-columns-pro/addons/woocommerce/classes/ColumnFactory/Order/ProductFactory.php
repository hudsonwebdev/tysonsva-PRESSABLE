<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\PostProperty;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Search;
use ACA\WC\Setting\ComponentFactory\Product\ProductProperty;
use ACA\WC\Setting\ComponentFactory\Product\SecondaryProductProperty;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class ProductFactory extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
    use WooCommerceGroupTrait;

    private ProductProperty $product_property;

    private SecondaryProductProperty $secondary_product_property;

    private AC\Setting\ComponentFactory\NumberOfItems $number_of_items;

    private AC\Setting\ComponentFactory\Separator $separator;

    private AC\Setting\ComponentFactory\PostLink $post_link;

    public function __construct(
        FeatureSettingBuilderFactory $feature_settings_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        ProductProperty $product_property,
        SecondaryProductProperty $secondary_product_property,
        AC\Setting\ComponentFactory\PostLink $post_link,
        AC\Setting\ComponentFactory\NumberOfItems $number_of_items,
        AC\Setting\ComponentFactory\Separator $separator
    ) {
        parent::__construct($feature_settings_builder_factory, $default_settings_builder);
        $this->product_property = $product_property;
        $this->secondary_product_property = $secondary_product_property;
        $this->post_link = $post_link;
        $this->number_of_items = $number_of_items;
        $this->separator = $separator;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->product_property->create($config))
            ->add($this->post_link->create($config))
            ->add($this->secondary_product_property->create($config))
            ->add($this->number_of_items->create($config))
            ->add($this->separator->create($config));
    }

    public function get_label(): string
    {
        return __('Products', 'woocommerce');
    }

    public function get_column_type(): string
    {
        return 'column-order_product';
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        $separator_config = $config;

        if ($config->get('secondary_product_property', '')) {
            $separator_config = new Config(
                array_merge($config->all(), ['separator' => AC\Setting\ComponentFactory\Separator::NONE])
            );
        }

        return parent::get_formatters($config)
            ->prepend(new Formatter\Order\Products())
            ->add(AC\Formatter\Collection\Separator::create_from_config($separator_config));
    }

    private function is_analytics_enabled(): bool
    {
        return get_option('woocommerce_analytics_enabled') !== 'no';
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        switch ($config->get('product_property', PostProperty::PROPERTY_TITLE)) {
            case PostProperty::PROPERTY_ID:
                return new Search\Order\Product\PostField(
                    'ID',
                    new Operators([
                        Operators::EQ,
                        Operators::NEQ,
                        Operators::GT,
                        Operators::GTE,
                        Operators::LT,
                        Operators::LTE,
                        Operators::BETWEEN,
                    ]),
                    Value::INT
                );
            case ProductProperty::TYPE_SKU:
                return new Search\Order\Product\PostMeta(
                    '_sku',
                    new Operators([
                        Operators::CONTAINS,
                        Operators::NOT_CONTAINS,
                        Operators::BEGINS_WITH,
                        Operators::ENDS_WITH,
                    ], false)
                );
            default:
                return $this->is_analytics_enabled()
                    ? new Search\Order\Product\Picker()
                    : new Search\Order\Product\PickerNonAnalytics();
        }
    }

}
