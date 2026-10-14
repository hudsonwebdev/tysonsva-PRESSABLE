<?php

declare(strict_types=1);

namespace ACA\DataSources\BulkDelete\Deletable\RequestHandler;

use ACA\DataSources;
use ACP\Editing\BulkDelete\RequestHandler;

class DataSource extends RequestHandler
{
    private DataSources\DataSource $data_source;

    public function __construct(DataSources\DataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    protected function delete($id, array $args = []): void
    {
        $id = (int)$id;

        $this->data_source->get_repository()->delete($id);
    }
}
