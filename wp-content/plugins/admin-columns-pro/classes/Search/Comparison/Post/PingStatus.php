<?php

declare(strict_types=1);

namespace ACP\Search\Comparison\Post;

class PingStatus extends CommentStatus
{
    protected function get_field(): string
    {
        return 'ping_status';
    }

}
