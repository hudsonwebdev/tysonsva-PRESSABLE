<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table\Filter;

use ACA\DataSources\Repository\Database\Table\Filter;

abstract class Aggregate implements Filter
{
    /**
     * @var Filter[]
     */
    protected array $filters;

    /**
     * @param Filter[] $filters
     */
    public function __construct(array $filters)
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    protected function add(Filter $filter): void
    {
        $this->filters[] = $filter;
    }

}
