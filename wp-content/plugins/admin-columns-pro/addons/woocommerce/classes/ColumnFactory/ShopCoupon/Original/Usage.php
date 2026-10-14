<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopCoupon\Original;

use AC\Setting\Config;
use ACA\WC\Editing;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;
use ACP\Sorting\Type\DataType;

class Usage extends OriginalColumnFactory
{
    private const META_KEY = 'usage_count';

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\ShopCoupon\Usage();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Number(self::META_KEY);
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta(self::META_KEY, new DataType(DataType::NUMERIC));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Numeric::for_post(self::META_KEY);
    }

}
