<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\User\ShopOrder;

use AC\Setting\Config;
use ACA\WC\ColumnFactory;
use ACA\WC\Metric;
use ACA\WC\Sorting;
use ACP;

class FirstOrder extends ColumnFactory\User\FirstOrderFactory
{
    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting\User\ShopOrder\FirstOrder();
    }

    protected function get_metric(Config $config): ?ACP\Metric\Config
    {
        return new Metric\Config\User\ShopOrder\OrderExtrema('min');
    }

}
