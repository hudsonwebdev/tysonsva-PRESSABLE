<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\User\ShopOrder;

use AC\Setting\Config;
use ACA\WC\ColumnFactory;
use ACA\WC\Metric;
use ACA\WC\Sorting;
use ACP;

class Orders extends ColumnFactory\User\Orders
{
    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\User\ShopOrder\OrderCount($config->get('order_status', []));
    }

    protected function get_metric(Config $config): ?ACP\Metric\Config
    {
        return new Metric\Config\User\ShopOrder\OrderCount($config->get('order_status', []));
    }

}
