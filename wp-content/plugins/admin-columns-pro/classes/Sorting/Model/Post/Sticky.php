<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

class Sticky extends Featured
{
    protected function get_featured_ids(): array
    {
        $stickies = get_option('sticky_posts', []);

        return is_array($stickies) ? $stickies : [];
    }

}
