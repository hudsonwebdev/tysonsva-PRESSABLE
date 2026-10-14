<?php

declare(strict_types=1);

namespace ACA\DataSources\Editing\Storage;

use ACA\DataSources\DataSource;
use ACP\Editing;

class Column implements Editing\Storage
{
    private DataSource $data_source;

    private string $column;

    public function __construct(DataSource $data_source, string $column)
    {
        $this->data_source = $data_source;
        $this->column = $column;
    }

    public function get(int $id): string
    {
        return (string)$this->data_source->get_repository()->get_value($id, $this->column);
    }

    public function update(int $id, $data): bool
    {
        $this->data_source->get_repository()->update_value($id, $this->column, $data);

        return true;
    }

}
