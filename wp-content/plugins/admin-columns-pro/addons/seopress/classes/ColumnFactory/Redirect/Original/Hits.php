<?php

declare(strict_types=1);

namespace ACA\SeoPress\ColumnFactory\Redirect\Original;

use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class Hits extends OriginalColumnFactory
{
    private const META_KEY = 'seopress_404_count';

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Number(self::META_KEY);
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Numeric::for_post(self::META_KEY);
    }

}
