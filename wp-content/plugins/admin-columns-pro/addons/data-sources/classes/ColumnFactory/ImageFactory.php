<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\ImageSize;
use AC\Setting\Config;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACP\Metric;
use ACP\Metric\ColumnType\Existence;

class ImageFactory extends ColumnFactory
{
    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add((new ImageSize(true))->create($config));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
