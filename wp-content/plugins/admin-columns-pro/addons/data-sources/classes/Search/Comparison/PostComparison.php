<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use ACA\DataSources;
use ACP\Helper;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PostComparison extends DataSources\Search\Comparison implements SearchableValues
{
    private ?array $post_types;

    public function __construct(string $id, ?array $post_types = [])
    {
        parent::__construct($id, new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]));
        $this->post_types = $post_types;
    }

    private function formatter(): PostTitle
    {
        return new Helper\Select\Post\LabelFormatter\PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->formatter()->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $post_types = ! empty($this->post_types) ? $this->post_types : ['any'];
        $args = [
            's'             => $search,
            'paged'         => $page,
            'post_type'     => $post_types,
            'search_fields' => ['post_title', 'ID'],
        ];

        return (new Helper\Select\Post\PaginatedFactory())->create($args, $this->formatter());
    }

}
