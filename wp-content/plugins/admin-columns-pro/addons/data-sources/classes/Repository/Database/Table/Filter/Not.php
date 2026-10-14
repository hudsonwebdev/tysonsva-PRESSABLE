<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table\Filter;

use ACA\DataSources\Repository\Database\Table\Column;
use ACA\DataSources\Repository\Database\Table\Filter;

class Not implements Filter
{
    private Filter $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function matches(Column $column): bool
    {
        return ! $this->filter->matches($column);
    }

}
