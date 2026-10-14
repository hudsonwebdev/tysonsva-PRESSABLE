<?php

declare(strict_types=1);

namespace ACP\Export\Exporter;

class JsonFactory
{
    public function create(TableData $table_data): Json
    {
        return new Json(
            $table_data->get_rows(),
            $table_data->get_headers()
        );
    }

}
