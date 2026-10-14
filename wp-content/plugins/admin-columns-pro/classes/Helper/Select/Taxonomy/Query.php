<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Taxonomy;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use WP_Term_Query;

class Query extends ArrayIterator implements Paginated
{
    protected WP_Term_Query $query;

    public function __construct(array $args = [])
    {
        $args = array_merge([
            'page'       => 1,
            'number'     => (new QueryTotalNumber())->apply_filter(),
            'search'     => '',
            'hide_empty' => 0,
            'taxonomy'   => null,
        ], $args);

        // calculate offset
        $args['offset'] = ($args['page'] - 1) * $args['number'];

        $this->query = new WP_Term_Query($args);

        $terms = $this->query->get_terms();

        parent::__construct(is_array($terms) ? $terms : []);
    }

    public function get_total_pages(): int
    {
        $taxonomy = $this->query->query_vars['taxonomy'][0];

        // Unset pagination in order to count the results
        $vars = $this->query->query_vars;
        $vars['page'] = 1;
        $vars['offset'] = 0;

        $count = wp_count_terms($taxonomy, $vars);

        if (is_wp_error($count)) {
            return 0;
        }

        return absint(ceil((int)$count / $this->query->query_vars['number']));
    }

    public function get_page(): int
    {
        return (int)$this->query->query_vars['page'];
    }

    public function is_last_page(): bool
    {
        return $this->get_total_pages() <= $this->get_page();
    }

}
