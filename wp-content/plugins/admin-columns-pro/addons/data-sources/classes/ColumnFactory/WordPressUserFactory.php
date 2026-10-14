<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\ForeignId;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
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

class WordPressUserFactory extends ColumnFactory
{
    private ACP\Setting\ComponentFactory\UserProperty $user_property_component;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        ACP\Setting\ComponentFactory\UserProperty $user_property_component
    ) {
        parent::__construct(
            $feature_setting_builder_factory,
            $default_settings_builder,
            $id,
            $label,
            $data_source,
            $group
        );

        $this->user_property_component = $user_property_component;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->user_property_component->create($config));
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        $paginated_options = new PaginatedOptions\Users();

        return new ACP\Editing\Service\User(
            (new ACP\Editing\View\AjaxSelect())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id),
            $paginated_options
        );
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        return parent::get_base_formatters($config)
            ->add(new ForeignId());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\UserComparison($this->get_id());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
