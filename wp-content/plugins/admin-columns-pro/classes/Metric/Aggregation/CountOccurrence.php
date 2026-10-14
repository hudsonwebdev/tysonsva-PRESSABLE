<?php

declare(strict_types=1);

namespace ACP\Metric\Aggregation;

use ACP\Metric\Aggregation;
use ACP\Query\Bindings;

class CountOccurrence implements Aggregation
{
    use Percentage;

    private string $value;

    private bool $as_percentage;

    public function __construct(string $value, bool $as_percentage = false)
    {
        $this->value = $value;
        $this->as_percentage = $as_percentage;
    }

    public function apply(Bindings $bindings, string $column): void
    {
        global $wpdb;

        $condition = sprintf('%s = %s', $column, $wpdb->prepare('%s', $this->value));

        if ($this->as_percentage) {
            $bindings->fields($this->percentage_of($condition));

            return;
        }

        $bindings->fields(sprintf('SUM(CASE WHEN %s THEN 1 ELSE 0 END)', $condition));
    }

}
