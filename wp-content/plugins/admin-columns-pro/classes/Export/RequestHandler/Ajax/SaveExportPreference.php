<?php

declare(strict_types=1);

namespace ACP\Export\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use ACP\Export\Repository\UserColumnStateRepository;
use ACP\Export\Type\ColumnState;
use ACP\Export\Type\ColumnStateCollection;
use ACP\Export\Type\ExportFormat;
use ACP\Export\UserPreference\ExportType;
use Exception;
use InvalidArgumentException;

final class SaveExportPreference implements RequestAjaxHandler
{
    private UserColumnStateRepository $state_repository;

    private ExportType $column_type_preference;

    public function __construct(UserColumnStateRepository $column_state_repository, ExportType $column_type_preference)
    {
        $this->state_repository = $column_state_repository;
        $this->column_type_preference = $column_type_preference;
    }

    public function handle(): void
    {
        $request = new Request();

        if (! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error('invalid nonce');
        }

        $id = (string)$request->filter('list_id', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $data = (string)$request->filter('data');
        $output_type = (string)$request->filter('output_type');

        if (! $data || ! ListScreenId::is_valid_id($id)) {
            wp_send_json_error();
        }

        try {
            $export_format = new ExportFormat($output_type);
        } catch (InvalidArgumentException $e) {
            wp_send_json_error('invalid export format');
        }

        $this->column_type_preference->set_export_type(new ListScreenId($id), $export_format);

        try {
            $this->state_repository->save(
                new ListScreenId($id),
                $this->get_column_states($data)
            );
        } catch (Exception $e) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }

    private function get_column_states(string $data): ColumnStateCollection
    {
        $collection = new ColumnStateCollection();
        foreach (json_decode($data, true) as $item) {
            $collection->add(new ColumnState($item['column_name'], $item['active']));
        }

        return $collection;
    }

}
