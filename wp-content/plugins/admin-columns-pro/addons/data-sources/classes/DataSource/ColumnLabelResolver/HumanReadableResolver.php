<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource\ColumnLabelResolver;

use ACA\DataSources\DataSource\ColumnLabelResolver;
use ACA\DataSources\Repository\Database\Table\Column;

final class HumanReadableResolver implements ColumnLabelResolver
{
    /**
     * Resolves from snake_case to Title Case to make labels more readable
     */
    public function resolve(Column $column): string
    {
        $label = str_replace('_', ' ', $column->get_name());
        $label = (string)preg_replace('/\s+/', ' ', $label);

        return ucwords(trim($label));
    }

}
