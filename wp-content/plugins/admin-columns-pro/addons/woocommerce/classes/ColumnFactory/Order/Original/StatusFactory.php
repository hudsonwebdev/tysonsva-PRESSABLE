<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\Order\Original;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class StatusFactory extends OriginalColumnFactory
{
    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new WC\Search\Order\Status();
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new WC\Value\Formatter\Order\StatusLabel());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select(wc_get_order_statuses()),
            new WC\Editing\Storage\Order\Status()
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new WC\Metric\Config\Order\OrderField(
            'status',
            new Metric\ColumnType\Choice(wc_get_order_statuses())
        );
    }

}
