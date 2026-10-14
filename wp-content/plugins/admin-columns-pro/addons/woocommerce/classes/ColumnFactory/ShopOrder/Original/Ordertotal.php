<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopOrder\Original;

use AC;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\Metric\Formatter\WcPriceFormatterFactory;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class Ordertotal extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new AC\Formatter\Post\Meta('_order_total'));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Decimal('_order_total');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Numeric::for_post('_order_total')
            ->with_formatter(new WcPriceFormatterFactory());
    }

}
