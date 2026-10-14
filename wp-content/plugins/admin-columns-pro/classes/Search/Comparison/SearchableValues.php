<?php

declare(strict_types=1);

namespace ACP\Search\Comparison;

use AC\Helper\Select\Options\Paginated;

interface SearchableValues
{
    public function format_label($value): string;

    public function get_values(string $search, int $page): Paginated;

}
