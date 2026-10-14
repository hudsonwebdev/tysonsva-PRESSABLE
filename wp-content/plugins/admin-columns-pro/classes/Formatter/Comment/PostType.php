<?php

declare(strict_types=1);

namespace ACP\Formatter\Comment;

use AC;
use AC\Exception\ValueNotFoundException;
use AC\Type\Value;

class PostType implements AC\Formatter
{
    public function format(Value $value)
    {
        $comment = get_comment($value->get_id());

        if (! $comment) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        return $value->with_value(get_post_type((int)$comment->comment_post_ID));
    }

}
