<?php

declare(strict_types=1);

namespace ACP\Search\Comparison;

use AC\Helper\Select\Options;

interface RemoteValues
{
    public function format_label(string $value): string;

    public function get_values(): Options;

}
