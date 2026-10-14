<?php

declare(strict_types=1);

namespace ACA\DataSources\Admin;

use ACA\DataSources\Type\DataSourceId;

interface PageFactory
{
    public function create(DataSourceId $id): Page;

}
