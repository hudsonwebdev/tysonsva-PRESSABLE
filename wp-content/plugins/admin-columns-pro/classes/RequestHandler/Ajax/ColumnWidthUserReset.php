<?php

declare(strict_types=1);

namespace ACP\RequestHandler\Ajax;

use AC\ColumnSize;
use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use InvalidArgumentException;

class ColumnWidthUserReset implements RequestAjaxHandler
{
    private ColumnSize\UserStorage $user_storage;

    private Nonce\Ajax $nonce;

    public function __construct(ColumnSize\UserStorage $user_storage, Nonce\Ajax $nonce)
    {
        $this->user_storage = $user_storage;
        $this->nonce = $nonce;
    }

    public function handle(): void
    {
        $request = new Request();

        if (! $this->nonce->verify($request)) {
            wp_send_json_error();
        }

        try {
            $id = new ListScreenId((string)$request->get('list_id'));
        } catch (InvalidArgumentException $e) {
            wp_send_json_error();
        }

        $this->user_storage->delete(
            $id,
            (string)$request->get('column_name')
        );

        wp_send_json_success();
    }

}
