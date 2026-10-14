<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table\Filter;

use ACA\DataSources\Repository\Database\Table\Column;

class All extends Aggregate
{
    public function matches(Column $column): bool
    {
        foreach ($this->filters as $filter) {
            if (! $filter->matches($column)) {
                return false;
            }
        }

        return true;
    }

}
