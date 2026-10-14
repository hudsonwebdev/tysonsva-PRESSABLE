<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Sql\Comparison;

interface Negatable
{
    public function is_negated(): bool;

}
