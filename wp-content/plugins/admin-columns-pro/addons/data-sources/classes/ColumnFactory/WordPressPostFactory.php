<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\ForeignId;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\PostExtendedProperty;
use AC\Setting\ComponentFactory\PostLink;
use AC\Setting\ComponentFactory\PostType;
use AC\Setting\ComponentFactory\PostTypeFactory;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Editing\PaginatedOptions;
use ACP\Metric;
use ACP\Metric\ColumnType\Existence;
use ACP\Sql\Alias;

class WordPressPostFactory extends ColumnFactory
{
    private PostExtendedProperty $post_property_component;

    private PostLink $post_link_component;

    private PostType $post_type_component;

    private DataSource\ColumnType\WordPressPostType $column_type;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        PostExtendedProperty $post_property_component,
        PostLink $post_link_component,
        PostTypeFactory $post_type_component,
        DataSource\ColumnType\WordPressPostType $column_type,
        ?Alias $alias = null
    ) {
        parent::__construct(
            $feature_setting_builder_factory,
            $default_settings_builder,
            $id,
            $label,
            $data_source,
            $group,
            $alias
        );

        $this->post_property_component = $post_property_component;
        $this->post_link_component = $post_link_component;
        $this->post_type_component = $post_type_component->create(true, true);
        $this->column_config = $column_type;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        $settings = parent::get_settings($config);

        if (! $this->column_config->get_post_types()) {
            $settings->add($this->post_type_component->create($config));
        }

        return $settings
            ->add($this->post_property_component->create($config))
            ->add($this->post_link_component->create($config));
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        return parent::get_base_formatters($config)
            ->add(new ForeignId());
    }

    private function get_related_post_types(Config $config): array
    {
        return ! empty($this->column_config->get_post_types())
            ? $this->column_config->get_post_types()
            : (array)$config->get('post_type', []);
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        $paginated_options = new PaginatedOptions\Posts($this->get_related_post_types($config));

        return new ACP\Editing\Service\Post(
            (new ACP\Editing\View\AjaxSelect())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id),
            $paginated_options
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\PostComparison($this->get_id(), $this->get_related_post_types($config));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
