<?php

declare(strict_types=1);

namespace ACA\WC\Subscriptions\ColumnFactory\OrderSubscription\Original;

use AC\Setting\Config;
use ACA\WC;
use ACP;
use ACP\Column\OriginalColumnFactory;

class Orders extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?ACP\Export\Service
    {
        return new WC\Export\StrippedTableValue('orders');
    }

}
