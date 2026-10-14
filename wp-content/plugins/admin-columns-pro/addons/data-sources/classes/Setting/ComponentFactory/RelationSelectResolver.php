<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use ACA\DataSources\DataSource;

final class RelationSelectResolver
{
    public function resolve(DataSource $data_source): RelationSelect
    {
        return new RelationSelect($data_source);
    }

}
