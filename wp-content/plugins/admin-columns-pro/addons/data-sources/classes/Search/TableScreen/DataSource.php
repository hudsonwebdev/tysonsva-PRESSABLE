<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\TableScreen;

use ACP\Search;

class DataSource extends Search\TableMarkup
{
    public function register(): void
    {
        parent::register();

        add_action('acp/data-sources/restrict_items', [$this, 'filters_markup']);
    }

}
