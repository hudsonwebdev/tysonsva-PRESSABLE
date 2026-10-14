<?php

declare(strict_types=1);

namespace ACA\DataSources\Search;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Comparison extends ACP\Search\Comparison
{
    private string $id;

    public function __construct(
        string $id,
        Operators $operators,
        ?string $value_type = null,
        ?Labels $labels = null
    ) {
        parent::__construct($operators, $value_type, $labels);

        $this->id = $id;
    }

    public function with_id(string $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $stmt = ACP\Search\Helper\Sql\ComparisonFactory::create($this->id, $operator, $value);

        $bindings = new Bindings();
        $bindings->where($stmt->prepare());

        return $bindings;
    }

}
