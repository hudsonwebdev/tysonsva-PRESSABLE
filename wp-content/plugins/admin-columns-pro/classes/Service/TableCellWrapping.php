<?php

declare(strict_types=1);

namespace ACP\Service;

use AC\ListScreen;
use AC\Registerable;

class TableCellWrapping implements Registerable
{
    public function register(): void
    {
        add_action('ac/admin_head', [$this, 'render_style']);
        add_filter('ac/table/body_class', [$this, 'add_wrapping_class'], 10, 2);
    }

    public function render_style(ListScreen $list_screen): void
    {
        // Clip wrapping style based on the preference 'auto width' or 'clip'
        if (! in_array($list_screen->get_preference('wrapping'), ['auto', 'clip'], true)) {
            return;
        }

        ?>
		<style>table.wp-list-table td {
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
			}
		</style>
        <?php
    }

    /**
     * @return mixed
     */
    public function add_wrapping_class($classes, $table)
    {
        $list_screen = $table->get_list_screen();

        if (! $list_screen) {
            return $classes;
        }

        $wrapping = $list_screen->get_preference('wrapping');

        if (! is_string($wrapping) || $wrapping === '') {
            return $classes;
        }

        return $classes . ' acp-wrap-' . sanitize_html_class($wrapping);
    }

}
