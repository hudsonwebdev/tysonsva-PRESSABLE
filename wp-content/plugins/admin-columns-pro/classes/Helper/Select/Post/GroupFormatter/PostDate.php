<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Post\GroupFormatter;

use ACP\Helper\Select\Post\GroupFormatter;
use DateTimeInterface;
use WP_Post;

class PostDate implements GroupFormatter
{
    public function format(WP_Post $post): string
    {
        $date = get_post_datetime($post);

        if (! $date instanceof DateTimeInterface) {
            return '';
        }

        return wp_date('F Y', $date->getTimestamp());
    }

}
