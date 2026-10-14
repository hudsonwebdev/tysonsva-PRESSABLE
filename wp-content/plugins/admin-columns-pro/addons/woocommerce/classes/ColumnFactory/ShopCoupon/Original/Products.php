<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopCoupon\Original;

use AC\Formatter\Collection\Separator;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\Search;
use ACA\WC\Value\Formatter\ShopCoupon\IncludedProductCollection;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class Products extends OriginalColumnFactory
{
    private const META_KEY = 'product_ids';

    protected function get_export(Config $config): ?FormatterCollection
    {
        return new FormatterCollection([
            new IncludedProductCollection(),
            new Separator(),
        ]);
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\ShopCoupon\Products(self::META_KEY);
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_post(self::META_KEY);
    }

}
