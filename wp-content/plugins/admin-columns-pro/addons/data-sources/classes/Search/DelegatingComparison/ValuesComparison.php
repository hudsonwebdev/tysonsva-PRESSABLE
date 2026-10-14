<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\DelegatingComparison;

use AC\Helper\Select\Options;
use ACA\DataSources\Search\Comparison;
use ACA\DataSources\Search\DelegatingComparison;
use ACA\DataSources\Search\QueryBindingsFactory;
use ACP\Search\Comparison\Values;
use InvalidArgumentException;

final class ValuesComparison extends DelegatingComparison implements Values
{
    private Values $values;

    public function __construct(Comparison $comparison, QueryBindingsFactory $factory)
    {
        if (! $comparison instanceof Values) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s.', Values::class));
        }

        parent::__construct($comparison, $factory);

        $this->values = $comparison;
    }

    public function get_values(): Options
    {
        return $this->values->get_values();
    }

}
