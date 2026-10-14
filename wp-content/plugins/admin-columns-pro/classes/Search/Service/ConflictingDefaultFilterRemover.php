<?php

declare(strict_types=1);

namespace ACP\Search\Service;

use AC\ListScreen;
use AC\Registerable;

final class ConflictingDefaultFilterRemover implements Registerable
{
    private ?ListScreen $list_screen = null;

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'on_list_screen'], 5);
    }

    public function on_list_screen(ListScreen $list_screen): void
    {
        if ($list_screen->get_meta_type() !== 'post') {
            return;
        }

        $this->list_screen = $list_screen;

        add_filter('disable_categories_dropdown', [$this, 'maybe_disable_categories_dropdown']);
        add_filter('disable_formats_dropdown', [$this, 'maybe_disable_formats_dropdown']);
    }

    public function maybe_disable_categories_dropdown(bool $disable): bool
    {
        return $disable || $this->has_taxonomy_column('category');
    }

    public function maybe_disable_formats_dropdown(bool $disable): bool
    {
        return $disable || $this->has_taxonomy_column('post_format');
    }

    private function has_taxonomy_column(string $taxonomy): bool
    {
        if ($this->list_screen === null) {
            return false;
        }

        foreach ($this->list_screen->get_columns() as $column) {
            $setting = $column->get_setting('taxonomy');

            if ($setting === null || ! $setting->has_input()) {
                continue;
            }

            if ((string)$setting->get_input()->get_value() === $taxonomy) {
                return true;
            }
        }

        return false;
    }

}
