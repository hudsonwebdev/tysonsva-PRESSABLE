<?php

declare(strict_types=1);

namespace ACP\Setting\ComponentFactory\Post;

use AC\Setting\ComponentFactory\BaseComponentFactory;
use AC\Setting\Config;
use AC\Setting\Control\Input;
use AC\Setting\Control\Input\OptionFactory;
use AC\Setting\Control\OptionCollection;

class ParentPostType extends BaseComponentFactory
{
    public const NAME = 'parent_post_type';

    public const ANY = 'any';

    protected function get_label(Config $config): ?string
    {
        return __('Parent Post Type', 'codepress-admin-columns');
    }

    protected function get_description(Config $config): ?string
    {
        return __(
            'The post types that can be selected as parent when editing.',
            'codepress-admin-columns'
        );
    }

    protected function get_input(Config $config): ?Input
    {
        return OptionFactory::create_select(
            self::NAME,
            $this->get_select_options(),
            (string)$config->get(self::NAME, '')
        );
    }

    private function get_select_options(): OptionCollection
    {
        return OptionCollection::from_array([
            ''        => __('Same Post Type', 'codepress-admin-columns'),
            self::ANY => __('All Post Types', 'codepress-admin-columns'),
        ]);
    }

}
