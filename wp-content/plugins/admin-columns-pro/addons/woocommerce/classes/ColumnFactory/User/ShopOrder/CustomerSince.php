<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\User\ShopOrder;

use AC\Setting\Config;
use ACA\WC\ColumnFactory;
use ACA\WC\Metric;
use ACA\WC\Sorting;
use ACP;

class CustomerSince extends ColumnFactory\User\CustomerSinceFactory
{
    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\User\ShopOrder\FirstOrder(['wc-completed']);
    }

    protected function get_metric(Config $config): ?ACP\Metric\Config
    {
        return new Metric\Config\User\ShopOrder\OrderExtrema('min');
    }

}
