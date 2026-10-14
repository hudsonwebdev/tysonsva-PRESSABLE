<?php

declare(strict_types=1);

namespace ACP\Metric\TableElement;

use AC\ListScreen;
use ACP\Settings\ListScreen\TableElement;

final class Metric extends TableElement
{
    public function __construct()
    {
        parent::__construct('hide_metric', __('Metric', 'codepress-admin-columns'), 'feature');
    }

    protected function get_default(ListScreen $list_screen): bool
    {
        return (bool) apply_filters('ac/metric/enable/default', parent::get_default($list_screen), $list_screen);
    }

}
