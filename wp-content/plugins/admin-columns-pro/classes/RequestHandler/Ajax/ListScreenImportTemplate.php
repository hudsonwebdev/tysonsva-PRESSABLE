<?php

declare(strict_types=1);

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Capabilities;
use AC\ListScreenCollection;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use AC\Type\ListScreenStatus;
use ACP\ListScreenRepository\TemplateJsonFile;
use ACP\Tools\ImportHandler;
use InvalidArgumentException;

class ListScreenImportTemplate implements RequestAjaxHandler
{
    use ImportMessageTrait;

    private AC\Nonce\Ajax $nonce;

    private TemplateJsonFile $template_repository;

    private ImportHandler $import_handler;

    public function __construct(
        AC\Nonce\Ajax $nonce,
        TemplateJsonFile $template_repository,
        ImportHandler $import_handler
    ) {
        $this->nonce = $nonce;
        $this->template_repository = $template_repository;
        $this->import_handler = $import_handler;
    }

    public function handle(): void
    {
        if (! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        $request = new Request();

        if (! $this->nonce->verify($request)) {
            wp_send_json_error();
        }

        try {
            $template = $this->template_repository->find(
                new ListScreenId(
                    (string)$request->get('list_id')
                )
            );
        } catch (InvalidArgumentException $e) {
            wp_send_json_error('ListScreen template not found.');
        }

        if (! $template) {
            wp_send_json_error('ListScreen template not found.');
        }

        $list_screen = $this->import_handler->import(
            $template,
            ['status' => ListScreenStatus::create_active()]
        );

        if (! $list_screen) {
            wp_send_json_error('ListScreen template could not be imported.');
        }

        wp_send_json_success(
            $this->create_success_message(new ListScreenCollection([$list_screen]))
        );
    }

}
