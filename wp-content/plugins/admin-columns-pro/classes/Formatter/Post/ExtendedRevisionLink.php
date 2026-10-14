<?php

declare(strict_types=1);

namespace ACP\Formatter\Post;

use AC;
use AC\Exception\ValueNotFoundException;
use AC\Type\Value;

class ExtendedRevisionLink implements AC\Formatter
{
    private AC\Value\Extended\ExtendedValue $extended_value;

    public function __construct(AC\Value\Extended\ExtendedValue $extended_value)
    {
        $this->extended_value = $extended_value;
    }

    public function format(Value $value)
    {
        $count = $value->get_value();

        if (! is_numeric($count)) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        $count = (int)$count;

        $label = sprintf(_n('%d revision', '%d revisions', $count, 'codepress-admin-columns'), $count);

        $link = $this->extended_value
            ->get_link($value->get_id(), $label)
            ->with_title(get_the_title($value->get_id()));

        $edit_link = get_edit_post_link($value->get_id());

        if ($edit_link) {
            $link = $link->with_edit_link($edit_link);
        }

        return $value->with_value(
            $link->render()
        );
    }

}
