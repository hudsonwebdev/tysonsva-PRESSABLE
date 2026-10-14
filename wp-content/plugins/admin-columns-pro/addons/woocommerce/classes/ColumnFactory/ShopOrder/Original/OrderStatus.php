<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopOrder\Original;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\Editing;
use ACA\WC\Value\Formatter\Order\StatusLabel;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class OrderStatus extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new StatusLabel());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\ShopOrder\Status();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Post\Status('shop_order');
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\PostField('post_status');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\Post\Status(wc_get_order_statuses());
    }

}
