<?php

declare(strict_types=1);

namespace ACP\Table;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use InvalidArgumentException;

class StickyTableRow implements AC\Registerable
{
    private Storage $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function register(): void
    {
        add_action('ac/table', [$this, 'register_screen_option']);

        $this->ajax_handler()->register();
    }

    private function ajax_handler(): AC\Ajax\Handler
    {
        $handler = new AC\Ajax\Handler();

        $handler
            ->set_action('acp_update_sticky_row_option')
            ->set_callback([$this, 'update_sticky_table']);

        return $handler;
    }

    public function preferences(): AC\Preferences\Preference
    {
        return (new AC\Preferences\SiteFactory())->create('show_sticky_table_row');
    }

    public function is_sticky(string $storage_key): bool
    {
        return (bool)apply_filters('ac/sticky_header/enable', (bool)$this->preferences()->find($storage_key));
    }

    public function update_sticky_table(): void
    {
        $this->ajax_handler()->verify_request();

        try {
            $list_screen = $this->storage->find(
                new ListScreenId(
                    (string)filter_input(INPUT_POST, 'layout')
                )
            );
        } catch (InvalidArgumentException $e) {
            wp_send_json_error();
        }

        if (! $list_screen || ! $list_screen->is_user_allowed(wp_get_current_user())) {
            wp_send_json_error();
        }

        $this->preferences()->save(
            $list_screen->get_table_id() . $list_screen->get_id(),
            'true' === filter_input(INPUT_POST, 'value')
        );

        wp_send_json_success();
    }

    public function register_screen_option(AC\Table\Screen $table): void
    {
        $list_screen = $table->get_list_screen();

        if (! $list_screen) {
            return;
        }

        $check_box = (new AC\Form\Element\Checkbox('acp_sticky_table_row'))
            ->set_options([
                'yes' => __('Sticky Headers', 'codepress-admin-columns'),
            ])
            ->set_value($this->is_sticky($list_screen->get_table_id() . $list_screen->get_id()) ? 'yes' : '');

        $table->register_screen_option($check_box);
    }

}
