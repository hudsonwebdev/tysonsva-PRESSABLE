<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Sql\Comparison;

class NotLike extends Like
{
    public function is_negated(): bool
    {
        return true;
    }

}
