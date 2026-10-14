<?php

declare(strict_types=1);

namespace ACP\Editing\Service\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select\Post\PaginatedFactory;

class PostParent extends BasicStorage implements PaginatedOptions
{
    private array $post_types;

    public function __construct(array $post_types)
    {
        parent::__construct(new Storage\Post\Field('post_parent'));

        $this->post_types = $post_types;
    }

    public function get_view(string $context): ?View
    {
        return (new View\AjaxSelect())->set_clear_button(true);
    }

    public function get_paginated_options(string $search, int $page, ?int $id = null): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->post_types,
        ]);
    }

    public function get_value(int $id)
    {
        $parent = get_post(parent::get_value($id));

        if (! $parent) {
            return false;
        }

        return [
            $parent->ID => $parent->post_title,
        ];
    }

}
