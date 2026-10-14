<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\User\ShopOrder;

use AC\Setting\Config;
use ACA\WC\ColumnFactory;
use ACA\WC\Metric;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class OrderCount extends ColumnFactory\User\OrderCountFactory
{
    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\User\ShopOrder\OrderCount($this->get_order_status($config));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\User\ShopOrder\OrderCount($this->get_order_status($config));
    }

    protected function get_metric(Config $config): ?ACP\Metric\Config
    {
        return new Metric\Config\User\ShopOrder\OrderCount($this->get_order_status($config));
    }

}
