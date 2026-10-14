<?php

declare(strict_types=1);

namespace ACP\Export;

use ACP\Export\Exporter\JsonFactory;
use ACP\Export\Exporter\TableData;

class ResponseFactory
{
    private JsonFactory $json_factory;

    public function __construct(JsonFactory $json_factory)
    {
        $this->json_factory = $json_factory;
    }

    public function create(TableData $table_data): void
    {
        $exporter = $this->json_factory->create($table_data);

        wp_send_json_success([
            'rows'               => $exporter->get_contents(),
            'headers'            => $exporter->get_headers(),
            'num_rows_processed' => $exporter->count_rows(),
        ]);
    }
}
