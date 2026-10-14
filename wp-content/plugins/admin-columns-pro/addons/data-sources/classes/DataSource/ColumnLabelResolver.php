<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use ACA\DataSources\Repository\Database\Table\Column;

interface ColumnLabelResolver
{
    public function resolve(Column $column): string;

}
