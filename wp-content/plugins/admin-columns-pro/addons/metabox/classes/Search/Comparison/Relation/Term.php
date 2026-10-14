<?php

declare(strict_types=1);

namespace ACA\MetaBox\Search\Comparison\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\MetaBox\Search;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use WP_Term;

class Term extends Search\Comparison\Relation
{
    public function format_label($value): string
    {
        $term = get_term($value);

        return $term instanceof WP_Term
            ? (new TermName())->format_label($term)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $related = $this->relation->get_related_field_settings();

        $taxonomies = [];

        if (isset($related['taxonomy']) && is_string($related['taxonomy'])) {
            $taxonomies = [$related['taxonomy']];
        }

        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $taxonomies,
        ]);
    }

}
