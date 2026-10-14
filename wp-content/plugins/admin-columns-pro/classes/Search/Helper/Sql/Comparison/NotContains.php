<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Sql\Comparison;

class NotContains extends Contains
{
    public function is_negated(): bool
    {
        return true;
    }

}
