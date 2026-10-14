<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\Media\Original;

use AC\Setting\Config;
use ACP;
use ACP\Editing;
use ACP\Metric;

class Date extends ACP\ColumnFactory\Post\Original\Date
{
    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\Service\Media\Date();
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new Metric\Config\Post\PublishDate();
    }

}
