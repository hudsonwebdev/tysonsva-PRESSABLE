<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order\Original;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class TotalFactory extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new WC\Value\Formatter\Order\Total());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new WC\Search\Order\Total();
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new WC\Metric\Config\Order\OrderField('total_amount', new WC\Metric\ColumnType\WcPrice());
    }

}
