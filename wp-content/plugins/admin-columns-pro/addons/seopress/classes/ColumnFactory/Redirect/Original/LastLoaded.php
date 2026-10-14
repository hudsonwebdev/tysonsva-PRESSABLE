<?php

declare(strict_types=1);

namespace ACA\SeoPress\ColumnFactory\Redirect\Original;

use AC;
use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;
use ACP\Sorting\Type\DataType;

class LastLoaded extends OriginalColumnFactory
{
    private const META_KEY = '_seopress_404_redirect_date_request';

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        $factory = new AC\Meta\QueryMetaFactory();
        $query = $factory->create_with_post_type(self::META_KEY, 'redirects');

        return new ACP\Search\Comparison\Meta\DateTime\Timestamp(self::META_KEY, $query);
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new ACP\Sorting\Model\Post\Meta(
            self::META_KEY,
            new DataType(DataType::NUMERIC)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        $metric = Metric\Config\Meta\Date::for_post(
            self::META_KEY,
        );

        return $metric instanceof Metric\Config\Meta\Date
            ? $metric->as_timestamp()
            : null;
    }

}
