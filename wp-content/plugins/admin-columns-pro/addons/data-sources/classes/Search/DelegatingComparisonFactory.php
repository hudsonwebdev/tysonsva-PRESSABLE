<?php

declare(strict_types=1);

namespace ACA\DataSources\Search;

use ACA\DataSources\Search\DelegatingComparison\SearchableValuesComparison;
use ACA\DataSources\Search\DelegatingComparison\ValuesComparison;
use ACP;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Comparison\Values;

final class DelegatingComparisonFactory
{
    public function create(
        Comparison $comparison,
        QueryBindingsFactory $factory
    ): ACP\Search\Comparison {
        if ($comparison instanceof SearchableValues) {
            return new SearchableValuesComparison($comparison, $factory);
        }

        if ($comparison instanceof Values) {
            return new ValuesComparison($comparison, $factory);
        }

        return new DelegatingComparison($comparison, $factory);
    }

}
