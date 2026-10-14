<?php

declare(strict_types=1);

namespace ACA\DataSources\Search;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Labels;
use ACP\Search\Value;

class DelegatingComparison extends ACP\Search\Comparison
{
    protected Comparison $comparison;

    protected QueryBindingsFactory $factory;

    public function __construct(
        Comparison $comparison,
        QueryBindingsFactory $factory
    ) {
        $this->comparison = $comparison;
        $this->factory = $factory;

        parent::__construct(
            $comparison->get_operators(),
            $comparison->get_value_type(),
            new Labels($comparison->get_labels())
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return $this->factory->create($this->comparison, $operator, $value);
    }

}
