<?php

declare(strict_types=1);

namespace ACP\Search\Helper\TaxQuery;

use ACP\Search\Value;

class Comparison
{
    protected string $taxonomy;

    protected string $operator;

    private Value $terms;

    private string $field;

    public function __construct(string $taxonomy, string $operator, Value $terms, string $field = 'term_id')
    {
        $this->taxonomy = $taxonomy;
        $this->operator = $operator;
        $this->terms = $terms;
        $this->field = $field;
    }

    public function get_expression(): array
    {
        $term = $this->terms->get_value();

        return [
            'taxonomy' => $this->taxonomy,
            'terms'    => [$this->is_numeric_field() ? (int)$term : $term],
            'operator' => $this->operator,
            'field'    => $this->field,
        ];
    }

    private function is_numeric_field(): bool
    {
        return in_array($this->field, ['term_id', 'term_taxonomy_id'], true);
    }

}
