<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\Addon;
use ACA\DataSources\ColumnFactoryResolver;
use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\ColumnType\ConfigurableType;
use ACA\DataSources\Setting\ColumnTypeOptionMap;
use ACA\DataSources\Setting\ComponentFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Sql\Alias;

class ConfigurableFactory extends DeferredColumnTypeFactory
{
    private ConfigurableType $column_type;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        ColumnFactoryResolver $column_factory_resolver,
        ColumnTypeOptionMap $registry,
        ComponentFactory\ColumnTypeSelectResolver $column_options_factory_resolver,
        ConfigurableType $column_type,
        ?Alias $alias = null
    ) {
        $this->column_type = $column_type;

        parent::__construct(
            $feature_setting_builder_factory,
            $default_settings_builder,
            $id,
            $label,
            $data_source,
            $group,
            $column_factory_resolver,
            $registry,
            $column_options_factory_resolver,
            $alias
        );
    }

    protected function get_default_column_type(): string
    {
        return $this->column_type->has_default_column_type()
            ? $this->column_type->get_default_column_type()
            : parent::get_default_column_type();
    }

    public function get_column_type(): string
    {
        return Addon::slug('configurable-' . $this->id);
    }

}
