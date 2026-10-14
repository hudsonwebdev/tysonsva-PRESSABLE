<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\User\Original;

use AC\Formatter\User\PostCountOriginal;
use AC\FormatterCollection;
use AC\Setting\Config;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;
use ACP\Sorting;
use ACP\Sorting\Model\QueryBindings;

class Posts extends OriginalColumnFactory
{
    protected function get_export(Config $config): ?FormatterCollection
    {
        return FormatterCollection::from_formatter(new PostCountOriginal());
    }

    protected function get_sorting(Config $config): ?QueryBindings
    {
        return new Sorting\Model\User\PostCount(['post'], ['publish', 'private']);
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\User\PostCount(['post'], ['publish', 'private']);
    }

}
