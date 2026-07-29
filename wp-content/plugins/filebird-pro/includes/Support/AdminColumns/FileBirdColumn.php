<?php

declare(strict_types=1);

namespace FileBird\Support\AdminColumns;

use AC;
use AC\Setting\Config;
use ACP;

class FileBirdColumn extends ACP\Column\AdvancedColumnFactory
{
    /**
     * Get the label for the column
     * This will be displayed in the column selector
     */
    public function get_label(): string
    {
        return __('FileBird Folder', 'filebird');
    }

    /**
     * Get the unique column type identifier
     * Must be unique across all columns
     */
    public function get_column_type(): string
    {
        return 'ac-filebird_folder';
    }

    /**
     * Get formatters to display the column value
     * This determines what data to show in the column
     */
    protected function get_formatters(Config $config): AC\Setting\FormatterCollection
    {
        $formatters = new AC\Setting\FormatterCollection();
        $formatters->add(new FileBirdFormatter());

        return $formatters;
    }

    /**
     * Enable inline editing (optional)
     * Return null to disable editing
     */
    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return null;
    }

    /**
     * Enable smart filtering (optional)
     * Return null to disable filtering
     */
    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return null;
    }

    /**
     * Enable sorting (optional)
     * Return null to disable sorting
     */
    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return null;
    }
}
