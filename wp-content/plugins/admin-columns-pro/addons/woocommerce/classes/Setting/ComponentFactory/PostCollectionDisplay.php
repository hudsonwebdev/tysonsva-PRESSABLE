<?php

declare(strict_types=1);

namespace ACA\WC\Setting\ComponentFactory;

use AC\Expression\StringComparisonSpecification;
use AC\Setting\AttributeCollection;
use AC\Setting\AttributeFactory;
use AC\Setting\Children;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\BaseComponentFactory;
use AC\Setting\ComponentFactory\LinkablePostProperty;
use AC\Setting\Config;
use AC\Setting\Control\Input;
use AC\Setting\Control\Input\OptionFactory;
use AC\Setting\Control\OptionCollection;

class PostCollectionDisplay extends BaseComponentFactory
{
    public const NAME = 'collection_display';
    public const COUNT = 'count';
    public const PROPERTIES = 'properties';

    private LinkablePostProperty $post_property;

    public function __construct(LinkablePostProperty $post_property)
    {
        $this->post_property = $post_property;
    }

    protected function get_label(Config $config): ?string
    {
        return __('Display', 'codepress-admin-columns');
    }

    protected function get_input(Config $config): ?Input
    {
        return OptionFactory::create_select(
            self::NAME,
            OptionCollection::from_array([
                self::COUNT      => __('Count', 'codepress-admin-columns'),
                self::PROPERTIES => __('Properties', 'codepress-admin-columns'),
            ]),
            $config->get(self::NAME, self::COUNT),
            null,
            false,
            new AttributeCollection([
                AttributeFactory::create_refresh(),
            ])
        );
    }

    protected function get_children(Config $config): ?Children
    {
        return new Children(
            new ComponentCollection([
                $this->post_property->create(
                    $config,
                    StringComparisonSpecification::equal(self::PROPERTIES)
                ),
            ])
        );
    }

}
