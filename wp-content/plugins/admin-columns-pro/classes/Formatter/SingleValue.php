<?php

declare(strict_types=1);

namespace ACP\Formatter;

use AC\Exception\ValueNotFoundException;
use AC\Formatter;
use AC\Formatter\Collection\Implode;
use AC\Type\Value;
use AC\Type\ValueCollection;

/**
 * Guarantees a single Value, so an item keeps its position within a collection.
 * Without this, AC\Formatter\Aggregate removes items whose formatter throws or returns a collection.
 */
class SingleValue implements Formatter
{
    private Formatter $formatter;

    private string $separator;

    public function __construct(Formatter $formatter, string $separator = ', ')
    {
        $this->formatter = $formatter;
        $this->separator = $separator;
    }

    public function format(Value $value): Value
    {
        try {
            $formatted = $this->formatter->format($value);
        } catch (ValueNotFoundException $e) {
            return $value->with_value('');
        }

        if ($formatted instanceof ValueCollection) {
            return (new Implode($this->separator))->format($formatted);
        }

        return $formatted instanceof Value
            ? $formatted
            : $value->with_value('');
    }

}
