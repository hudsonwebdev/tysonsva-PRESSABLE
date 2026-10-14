<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACA\WC\Value\Formatter;
use ACP;
use ACP\ConditionalFormat\Formatter\FloatFormatter;
use ACP\Metric;

class DiscountTotalFactory extends ACP\Column\AdvancedColumnFactory
{
    use WooCommerceGroupTrait;

    public function get_label(): string
    {
        return __('Discount Total', 'codepress-admin-columns');
    }

    public function get_column_type(): string
    {
        return 'column-order_discount';
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new Formatter\Order\TotalDiscount())
            ->add(new Formatter\WcPrice());
    }

    protected function get_conditional_format(Config $config): ?ACP\ConditionalFormat\FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(new FloatFormatter());
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\Order\OperationalData(
            OrderOperationalData::DISCOUNT_TOTAL_AMOUNT,
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Order\Discount();
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new WC\Metric\Config\Order\OperationalData(
            'discount_total_amount',
            new WC\Metric\ColumnType\WcPrice()
        );
    }

}
