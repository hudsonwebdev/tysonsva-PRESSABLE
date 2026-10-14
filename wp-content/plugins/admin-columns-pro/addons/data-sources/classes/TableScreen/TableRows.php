<?php

declare(strict_types=1);

namespace ACA\DataSources\TableScreen;

use AC;
use AC\Request;

class TableRows extends AC\TableScreen\TableRows
{
    public function register(): void
    {
        // Run handle after all other manage_value hooks have been registered (e.g. conditional formatting)
        add_action('current_screen', [$this, 'handle_request'], 11);
    }

    public function handle_request(): void
    {
        remove_action('current_screen', [$this, __FUNCTION__], 11);

        $this->handle(new Request());
    }

}
