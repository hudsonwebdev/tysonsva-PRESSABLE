<?php

declare(strict_types=1);

namespace ACA\DataSources\Asset\Script;

use AC;
use AC\Asset\Location;
use ACA\DataSources\Addon;

class DataSourceTable extends AC\Asset\Script
{
    public function __construct(
        Location $location,
        array $dependencies = [],
        bool $in_footer = false
    ) {
        parent::__construct(
            Addon::slug('table'),
            $location,
            $dependencies,
            $in_footer
        );
    }

}
