<?php

declare(strict_types=1);

namespace ACA\DataSources\Editing\Strategy;

use ACA\DataSources;
use ACA\DataSources\Editing;
use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class DataSource implements Strategy
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

    public function user_can_edit_item(int $id): bool
    {
        return $this->user_can_edit();
    }

    public function user_can_edit(): bool
    {
        return $this->capabilities_checker->can_edit($this->data_source->get_id());
    }

    public function get_query_request_handler(): RequestHandler
    {
        return new Editing\RequestHandler\DataSource($this->data_source);
    }

}
