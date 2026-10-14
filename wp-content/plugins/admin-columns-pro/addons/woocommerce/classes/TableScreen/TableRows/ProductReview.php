<?php

declare(strict_types=1);

namespace ACA\WC\TableScreen\TableRows;

use AC\Request;
use AC\TableScreen\TableRows;

class ProductReview extends TableRows
{
    public function register(): void
    {
        ob_start();
        add_action('parse_comment_query', [$this, 'handle_request']);
    }

    public function handle_request(): void
    {
        ob_clean();
        $this->handle(new Request());
    }

}
