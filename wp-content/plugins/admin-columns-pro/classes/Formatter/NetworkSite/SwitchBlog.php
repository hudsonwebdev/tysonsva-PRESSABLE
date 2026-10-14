<?php

declare(strict_types=1);

namespace ACP\Formatter\NetworkSite;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Formatter\Aggregate;
use AC\Type\Value;

class SwitchBlog implements Formatter
{
    private Aggregate $formatter;

    public function __construct(array $formatters)
    {
        $this->formatter = Aggregate::from_array($formatters);
    }

    public function format(Value $value): Value
    {
        switch_to_blog($value->get_id());

        $formatted = $this->formatter->format($value);

        restore_current_blog();

        if (! $formatted instanceof Value) {
            throw ValueNotFoundException::from_id($value->get_id());
        }

        return $formatted;
    }
}
