<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopOrder\Original;

use AC\Formatter\Post\Property;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class OrderDate extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new Property('post_date'));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Post\Date\PostDate('shop_order');
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\Post\PublishDate();
    }

}
