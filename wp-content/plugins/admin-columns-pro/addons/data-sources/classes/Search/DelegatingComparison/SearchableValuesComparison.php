<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\DelegatingComparison;

use AC\Helper\Select\Options\Paginated;
use ACA\DataSources\Search\Comparison;
use ACA\DataSources\Search\DelegatingComparison;
use ACA\DataSources\Search\QueryBindingsFactory;
use ACP\Search\Comparison\SearchableValues;
use InvalidArgumentException;

class SearchableValuesComparison extends DelegatingComparison implements SearchableValues
{
    private SearchableValues $searchable_values;

    public function __construct(Comparison $comparison, QueryBindingsFactory $factory)
    {
        if (! $comparison instanceof SearchableValues) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s.', SearchableValues::class));
        }

        parent::__construct($comparison, $factory);

        $this->searchable_values = $comparison;
    }

    public function format_label($value): string
    {
        return $this->searchable_values->format_label($value);
    }

    public function get_values(string $search, int $page): Paginated
    {
        return $this->searchable_values->get_values($search, $page);
    }

}
