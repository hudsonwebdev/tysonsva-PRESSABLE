<?php

declare(strict_types=1);

namespace ACA\GravityForms\Utils;

use AC\ListScreen;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Type\ListScreenId;
use ACA\GravityForms\TableScreen;

final class ActiveListScreenResolver
{
    private Storage $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Resolve the AC list screen the user is currently viewing for this form.
     * Prefers the `layout` URL parameter when valid for the given table id,
     * otherwise returns the first list screen registered for the table.
     * Returns null when no list screens exist yet.
     */
    public function resolve(TableScreen\Entry $table_screen): ?ListScreen
    {
        $table_id = $table_screen->get_id();

        $layout_param = (new Request())->get('layout');

        if (is_string($layout_param) && $layout_param !== '' && ListScreenId::is_valid_id($layout_param)) {
            $list_screen = $this->storage->find(new ListScreenId($layout_param));

            if ($list_screen && (string)$list_screen->get_table_screen()->get_id() === (string)$table_id) {
                return $list_screen;
            }
        }

        $collection = $this->storage->find_all_by_table_id($table_id);

        foreach ($collection as $list_screen) {
            return $list_screen;
        }

        return null;
    }

}
