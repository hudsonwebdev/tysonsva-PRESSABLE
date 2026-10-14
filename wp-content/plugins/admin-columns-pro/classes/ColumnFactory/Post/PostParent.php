<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\Post;

use AC;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\Config;
use AC\Type\PostTypeSlug;
use ACP;
use ACP\Column\EnhancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Editing;
use ACP\Search;
use ACP\Setting\ComponentFactory\Post\ParentPostType;
use ACP\Sorting;

class PostParent extends EnhancedColumnFactory
{
    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    private PostTypeSlug $post_type;

    private ParentPostType $parent_post_type;

    public function __construct(
        AC\ColumnFactory\Post\ParentFactory $column_factory,
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        PostTypeSlug $post_type,
        ParentPostType $parent_post_type
    ) {
        parent::__construct($column_factory, $feature_setting_builder_factory);

        $this->post_type = $post_type;
        $this->parent_post_type = $parent_post_type;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
                     ->merge(new ComponentCollection([$this->parent_post_type->create($config)]));
    }

    private function get_editable_post_types(Config $config): array
    {
        return ParentPostType::ANY === $config->get(ParentPostType::NAME)
            ? array_values(get_post_types(['show_ui' => true]))
            : [(string)$this->post_type];
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return new FormatterCollection([
            new AC\Formatter\Post\Property('post_parent'),
            new AC\Formatter\Post\PostTitle(),
        ]);
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\Service\Post\PostParent($this->get_editable_post_types($config));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\Post\PostParent((string)$this->post_type);
    }

    protected function get_sorting(Config $config): ?Sorting\Model\QueryBindings
    {
        return new Sorting\Model\Post\PostParent();
    }

}
