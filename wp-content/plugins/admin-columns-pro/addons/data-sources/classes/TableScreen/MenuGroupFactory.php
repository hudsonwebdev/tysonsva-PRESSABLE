<?php

declare(strict_types=1);

namespace ACA\DataSources\TableScreen;

use AC;
use AC\Admin\Type\MenuGroup;
use ACA\DataSources;
use ACA\DataSources\Addon;

final class MenuGroupFactory implements AC\Admin\MenuGroupFactory
{
    public function create(AC\TableScreen $table_screen): ?MenuGroup
    {
        if ($table_screen instanceof DataSources\TableScreen) {
            return new MenuGroup(
                Addon::slug('menu'),
                __('Custom List Tables', 'codepress-admin-columns'),
                13,
                'dashicons-database'
            );
        }

        return null;
    }

}
