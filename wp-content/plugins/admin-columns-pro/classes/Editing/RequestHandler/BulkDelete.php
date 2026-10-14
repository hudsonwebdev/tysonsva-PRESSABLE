<?php

declare(strict_types=1);

namespace ACP\Editing\RequestHandler;

use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing;
use ACP\Editing\RequestHandler;
use InvalidArgumentException;

class BulkDelete implements RequestHandler
{
    private Storage $storage;

    private Editing\BulkDelete\AggregateFactory $aggregate_factory;

    public function __construct(Storage $storage, Editing\BulkDelete\AggregateFactory $aggregate_factory)
    {
        $this->storage = $storage;
        $this->aggregate_factory = $aggregate_factory;
    }

    public function handle(Request $request)
    {
        $response = new Response\Json();

        try {
            $list_screen = $this->storage->find(
                new ListScreenId(
                    (string)$request->get('layout')
                )
            );
        } catch (InvalidArgumentException $e) {
            $response
                ->set_message('Invalid table.')
                ->error();
        }

        if (! $list_screen || ! $list_screen->is_user_allowed(wp_get_current_user())) {
            $response->error();
        }

        $deletable = $this->aggregate_factory->create(
            $list_screen->get_table_screen()
        );

        if (! $deletable) {
            $response
                ->set_message(__('Table does not support bulk delete.', 'codepress-admin-columns'))
                ->error();
        }

        if (! $deletable->user_can_delete()) {
            $response
                ->set_message(__('Current user has no delete permissions.', 'codepress-admin-columns'))
                ->error();
        }

        $deletable->get_delete_request_handler()->handle($request);
    }

}
