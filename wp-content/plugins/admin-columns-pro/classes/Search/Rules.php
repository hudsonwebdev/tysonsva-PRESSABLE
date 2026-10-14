<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Exception\IteratorException;
use AC\TypedArrayIterator;

final class Rules extends TypedArrayIterator
{
    public function __construct(array $array)
    {
        parent::__construct($array, Rule::class);
    }

    public function current(): Rule
    {
        $current = parent::current();

        if (! $current instanceof Rule) {
            throw IteratorException::from_current();
        }

        return $current;
    }

}
