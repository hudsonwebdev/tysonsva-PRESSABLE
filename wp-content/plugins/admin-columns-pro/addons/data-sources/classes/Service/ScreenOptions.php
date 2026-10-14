<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\DataSources;

final class ScreenOptions implements Registerable
{
    private DataSources\DataSource $data_source;

    public function __construct(DataSources\DataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    private function get_option_name(): string
    {
        return $this->data_source->get_id() . '_per_page';
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'set_screen_options']);
        add_filter('set_screen_option_' . $this->get_option_name(), [$this, 'get_screen_option_per_page'], 10, 3);
    }

    public function get_screen_option_per_page($screen_option, $option, $value): int
    {
        return (int)$value;
    }

    public function set_screen_options(ListScreen $list_screen): void
    {
        $table_screen = $list_screen->get_table_screen();

        if (! $table_screen instanceof DataSources\TableScreen ||
             ! $table_screen->get_data_source()->get_id()->equals($this->data_source->get_id())) {
            return;
        }

        add_screen_option('per_page', [
            'label'   => __('Number of items per page:'),
            'default' => 20,
            'option'  => $this->get_option_name(),
        ]);
    }

}
