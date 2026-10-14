<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use ACA\DataSources\DataSource;

final class ColumnResolver
{
    public function resolve(DataSource $data_source): Column
    {
        return new Column($data_source);
    }

}
