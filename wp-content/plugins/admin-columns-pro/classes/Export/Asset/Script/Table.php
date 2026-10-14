<?php

declare(strict_types=1);

namespace ACP\Export\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use ACP\Export\Type\ExportFormat;

final class Table extends Script
{
    public const NONCE_ACTION = 'acp_export_listscreen_export';

    /**
     * @var array [ $column_name => $column_label, ... ]
     */
    private array $columns;

    private bool $show_button;

    private int $items_per_iteration;

    private ExportFormat $export_format;

    public function __construct(
        string $handle,
        Location $location,
        int $items_per_iteration,
        array $columns,
        bool $show_button,
        ExportFormat $export_format
    ) {
        parent::__construct($handle, $location, ['jquery']);

        $this->columns = $columns;
        $this->show_button = $show_button;
        $this->items_per_iteration = $items_per_iteration;
        $this->export_format = $export_format;
    }

    public function register(): void
    {
        parent::register();

        $this->add_inline_variable('acp_export', [
            'num_iterations' => $this->items_per_iteration,
            'nonce'          => wp_create_nonce(self::NONCE_ACTION),
            'columns'        => $this->columns,
            'show_button'    => $this->show_button,
            'export_type'    => $this->export_format->get_format(),
            'csv_delimiter'  => apply_filters('ac/export/exporter_csv/delimiter', ','),
        ]);

        $this->localize('acp_export_i18n', new Script\Localize\Translation([
            'dismiss'       => __('Dismiss this notice.'),
            'export'        => __('Export', 'codepress-admin-columns'),
            'export_to_csv' => __('Export to CSV', 'codepress-admin-columns'),
            'export_error'  => __(
                'Something went wrong during exporting. Please try again.',
                'codepress-admin-columns'
            ),
            'export_incomplete' => __(
                'The export is incomplete: only {0} of {1} items were received. No file has been created. Please try again.',
                'codepress-admin-columns'
            ),
            'export_invalid_response' => __(
                'The server returned an unexpected response. The export could not be completed.',
                'codepress-admin-columns'
            ),
            'processed'        => __('Processed {0} of {1} items ({2}%).', 'codepress-admin-columns'),
            'exporting'        => __('Exporting current list of items.', 'codepress-admin-columns'),
            'export_completed' => __('Exported {0} items', 'codepress-admin-columns'),
            'download_file'    => __('Download File', 'codepress-admin-columns'),
            'failed'           => __('Failed', 'codepress-admin-columns'),
            'done'             => __('Done', 'codepress-admin-columns'),
            'cancel'           => __('Cancel', 'codepress-admin-columns'),
            'leaving'          => __(
                'You are currently generating an export file. Leaving the page will cancel this process. Are you sure you want to leave the page?',
                'codepress-admin-columns'
            ),
            'affected_items' => _x('This will affect {0}', 'export', 'codepress-admin-columns'),
            'items'          => __('{0} items', 'codepress-admin-columns'),
            'item'           => __('1 item', 'codepress-admin-columns'),
        ]));
    }

}
