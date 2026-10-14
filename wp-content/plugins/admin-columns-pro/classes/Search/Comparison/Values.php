<?php

declare(strict_types=1);

namespace ACP\Search\Comparison;

use AC\Helper\Select\Options;

interface Values
{
    public function get_values(): Options;

}
