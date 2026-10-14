<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting\ComponentFactory;

use ACA\DataSources\DataSource;

final class AttributeRelationSelectResolver
{
    public function resolve(DataSource $data_source, string $attribute_column_id): AttributeRelationSelect
    {
        return new AttributeRelationSelect($data_source, $attribute_column_id);
    }

}
