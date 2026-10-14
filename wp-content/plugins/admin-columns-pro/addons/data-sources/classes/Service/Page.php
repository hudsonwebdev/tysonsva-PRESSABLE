<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC\Registerable;
use ACA\DataSources\Addon;
use ACA\DataSources\Admin\Page\Menu;
use ACA\DataSources\CapabilitiesChecker;
use ACA\DataSources\DataSourceRegistry;
use ACA\DataSources\ListTableFactory;

final class Page implements Registerable
{
    private DataSourceRegistry $data_sources;

    private ListTableFactory $list_table_factory;

    private CapabilitiesChecker $capabilities_checker;

    public function __construct(
        ListTableFactory $list_table_factory,
        DataSourceRegistry $data_sources,
        CapabilitiesChecker $capabilities_checker
    ) {
        $this->list_table_factory = $list_table_factory;
        $this->data_sources = $data_sources;
        $this->capabilities_checker = $capabilities_checker;
    }

    public function register(): void
    {
        add_action('current_screen', [$this, 'set_list_table'], 100); // Admin Columns is now loaded
        add_action('admin_menu', [$this, 'register_pages']);
    }

    private function get_current_page(): string
    {
        return (string)filter_input(INPUT_GET, 'page');
    }

    public function register_pages(): void
    {
        foreach ($this->data_sources as $data_source) {
            $id = $data_source->get_id();

            if (! $this->data_sources->has_page($id)) {
                continue;
            }

            $page = $this->data_sources->get_page($id);
            $capability = $this->capabilities_checker->get_read($id);
            $callback = [$this, 'display'];

            if ($page instanceof Menu) {
                add_menu_page(
                    $page->get_title(),
                    $page->get_menu_title(),
                    $capability,
                    $page->get_slug(),
                    $callback,
                    $page->get_icon(),
                    $page->get_position()
                );
            } else {
                add_submenu_page(
                    $page->get_parent_slug(),
                    $page->get_title(),
                    $page->get_menu_title(),
                    $capability,
                    $page->get_slug(),
                    $callback,
                    $page->get_position()
                );
            }
        }
    }

    public function display(): void
    {
        global $wp_list_table;

        $page = $this->data_sources->find_page_by_slug($this->get_current_page());

        if (! $page) {
            return;
        }

        $title = esc_html($page->get_title());
        $form_id = Addon::slug('table-form');

        echo <<<AC
			<div class="wrap">
				<h1>$title</h1>
				<hr class="wp-header-end">
				<form id="$form_id">
			AC;

        $wp_list_table->display();

        echo <<<AC
				</form>
			</div>
			AC;
    }

    public function set_list_table(): void
    {
        global $wp_list_table;

        $page = $this->get_current_page();

        if (! $page || ! $this->list_table_factory->can_create($page)) {
            return;
        }

        $wp_list_table = $this->list_table_factory->create($page);
        $wp_list_table->prepare_items();
    }

}
