<?php

declare(strict_types=1);

namespace ACP\QuickAdd\Model\Create;

use ACP\QuickAdd\Model\Create;
use LogicException;
use RuntimeException;
use WP_User;

class Post implements Create
{
    protected string $post_type;

    public function __construct(string $post_type)
    {
        if (! post_type_exists($post_type)) {
            throw new LogicException('Post Type does not exists.');
        }

        $this->post_type = $post_type;
    }

    public function create(): int
    {
        $args = [
            'post_type' => $this->post_type,
        ];

        if (post_type_supports($this->post_type, 'title')) {
            $args['post_title'] = __('(no title)');
        }

        add_filter('wp_insert_post_empty_content', '__return_false');

        $id = wp_insert_post($args, true);

        remove_filter('wp_insert_post_empty_content', '__return_false');

        if (is_wp_error($id)) {
            throw new RuntimeException($id->get_error_message());
        }

        return (int)$id;
    }

    public function has_permission(WP_User $user): bool
    {
        $post_type_object = get_post_type_object($this->post_type);

        if (! $post_type_object) {
            return false;
        }

        return user_can($user, $post_type_object->cap->create_posts);
    }

}
