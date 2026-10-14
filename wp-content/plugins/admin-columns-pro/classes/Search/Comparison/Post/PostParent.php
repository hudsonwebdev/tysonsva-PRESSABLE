<?php

declare(strict_types=1);

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Post;

class PostParent extends PostField implements SearchableValues
{
    private string $current_post_type;

    public function __construct(string $current_post_type)
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            null,
            new Labels([
                Operators::IS_EMPTY     => __('Has No Parent', 'codepress-admin-columns'),
                Operators::NOT_IS_EMPTY => __('Has Parent', 'codepress-admin-columns'),
            ])
        );

        $this->current_post_type = $current_post_type;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        if (Operators::IS_EMPTY === $operator) {
            $operator = Operators::EQ;
            $value = new Value(0, $value->get_type());
        }

        if (Operators::NOT_IS_EMPTY === $operator) {
            $operator = Operators::NEQ;
            $value = new Value(0, $value->get_type());
        }

        return parent::create_query_bindings($operator, $value);
    }

    protected function get_field(): string
    {
        return 'post_parent';
    }

    private function formatter(): PostTitle
    {
        return new PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post instanceof WP_Post
            ? $this->formatter()->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        [$parent_ids, $parent_post_types] = $this->get_parents();

        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $parent_post_types,
            // A non-existing ID makes sure no results are returned when there are no parents
            'post__in' => $parent_ids ?: [0],
        ], $this->formatter());
    }

    /**
     * Parents can be of a different post type than their children, hence the post types are
     * resolved from the parents themselves.
     *
     * @return array{0: int[], 1: string[]}
     */
    private function get_parents(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT DISTINCT parent.ID, parent.post_type
             FROM $wpdb->posts AS child
             INNER JOIN $wpdb->posts AS parent ON parent.ID = child.post_parent
             WHERE child.post_type = %s
                AND child.post_parent > 0",
            $this->current_post_type
        );

        $ids = [];
        $post_types = [];

        foreach ($wpdb->get_results($sql) as $parent) {
            $ids[] = (int)$parent->ID;
            $post_types[$parent->post_type] = $parent->post_type;
        }

        return [$ids, array_values($post_types)];
    }

}
