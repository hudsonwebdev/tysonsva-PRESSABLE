<?php

declare(strict_types=1);

namespace ACA\WC\Value\Formatter\ProductReview;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Type\Value;

class ReviewType implements Formatter
{
    public function format(Value $value)
    {
        $comment = get_comment($value->get_id());

        if (! $comment) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        return $value->with_value($comment->comment_type);
    }

}
