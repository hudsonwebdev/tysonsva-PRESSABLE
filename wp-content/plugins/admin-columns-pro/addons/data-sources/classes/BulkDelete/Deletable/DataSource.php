<?php

declare(strict_types=1);

namespace ACA\DataSources\BulkDelete\Deletable;

use ACA\DataSources;
use ACP\Editing;
use ACP\Editing\BulkDelete\Deletable;

class DataSource implements Deletable
{
    private DataSources\DataSource $data_source;

    private DataSources\CapabilitiesChecker $capabilities_checker;

    public function __construct(
        DataSources\DataSource $data_source,
        DataSources\CapabilitiesChecker $capabilities_checker
    ) {
        $this->data_source = $data_source;
        $this->capabilities_checker = $capabilities_checker;
    }

    public function user_can_delete(): bool
    {
        return $this->capabilities_checker->can_delete($this->data_source->get_id());
    }

    public function get_delete_request_handler(): Editing\BulkDelete\RequestHandler
    {
        return new RequestHandler\DataSource($this->data_source);
    }

    public function get_query_request_handler(): Editing\RequestHandler
    {
        return new DataSources\Editing\RequestHandler\DataSource($this->data_source);
    }

}
