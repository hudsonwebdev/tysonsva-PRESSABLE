<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC\ListScreen;
use AC\Registerable;
use AC\Request;
use AC\TableScreen;
use ACA\GravityForms\TableScreen\Entry;
use ACA\GravityForms\Utils\ActiveListScreenResolver;

final class BaselineSync implements Registerable
{
    private GridColumnMetaSync $sync;

    private ActiveListScreenResolver $list_screen_resolver;

    public function __construct(
        GridColumnMetaSync $sync,
        ActiveListScreenResolver $list_screen_resolver
    ) {
        $this->sync = $sync;
        $this->list_screen_resolver = $list_screen_resolver;
    }

    public function register(): void
    {
        add_action('ac/list_screen/saved', [$this, 'on_list_screen_saved'], 10, 1);
        add_action('ac/list_screen/created', [$this, 'on_list_screen_changed']);
        add_action('ac/list_screen/deleted', [$this, 'on_list_screen_changed']);
        add_action('ac/table/screen', [$this, 'on_entry_list_render'], 20);
    }

    public function on_list_screen_saved(ListScreen $list_screen): void
    {
        $this->on_list_screen_changed($list_screen);
    }

    /**
     * Realign grid column meta after a list screen save/create/delete. When
     * the deleted screen was the only one, the resolver returns null and we
     * skip the write; the next entry-list render will pick a new baseline.
     */
    public function on_list_screen_changed(ListScreen $list_screen): void
    {
        $table_screen = $list_screen->get_table_screen();

        if (! $table_screen instanceof Entry) {
            return;
        }

        $active = $this->list_screen_resolver->resolve($table_screen);

        if (! $active) {
            return;
        }

        $this->sync->sync($table_screen, $active);
    }

    /**
     * Realign grid column meta on every entry-list page load except the
     * capture request itself ({@see CaptureRequestDetector} owns that path).
     */
    public function on_entry_list_render(TableScreen $table_screen): void
    {
        if (! $table_screen instanceof Entry) {
            return;
        }

        if ((new Request())->get('save-default-headings') === '1') {
            return;
        }

        $active = $this->list_screen_resolver->resolve($table_screen);

        if (! $active) {
            return;
        }

        $this->sync->sync($table_screen, $active);
    }

}
