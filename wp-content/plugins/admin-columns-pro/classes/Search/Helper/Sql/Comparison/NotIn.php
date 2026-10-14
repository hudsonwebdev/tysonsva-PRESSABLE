<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Sql\Comparison;

class NotIn extends In
{
    public function is_negated(): bool
    {
        return true;
    }

}
