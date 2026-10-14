<?php

declare(strict_types=1);

namespace ACA\WC\Export\Strategy;

use ACP\Export\Strategy\Comment;

class ProductReview extends Comment
{
    public function handle_export(): void
    {
        ob_start();
        parent::handle_export();
    }

    public function comments_query($query): void
    {
        if ($query->query_vars['count']) {
            return;
        }

        ob_get_clean();
        parent::comments_query($query);
    }

}
