<?php

declare(strict_types=1);

namespace ACA\DataSources\Repository\Database\Table\Filter;

use ACA\DataSources\Repository\Database\Table\Column;
use ACA\DataSources\Repository\Database\Table\Filter;

/**
 * Filter by column name.
 */
class Name implements Filter
{
    /**
     * @var string[]
     */
    protected array $column_names;

    public function __construct(array $column_names)
    {
        foreach ($column_names as $column_name) {
            $this->add($column_name);
        }
    }

    private function add(string $column_name): void
    {
        $this->column_names[] = $column_name;
    }

    public function matches(Column $column): bool
    {
        return in_array($column->get_name(), $this->column_names, true);
    }

}
