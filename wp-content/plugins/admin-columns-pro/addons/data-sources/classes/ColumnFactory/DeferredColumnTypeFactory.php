<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Column;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\ColumnFactoryResolver;
use ACA\DataSources\DataSource;
use ACA\DataSources\Setting\ColumnTypeOptionMap;
use ACA\DataSources\Setting\ComponentFactory;
use ACP\Column\AdvancedColumn;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Editing\Service;
use ACP\Metric;
use ACP\Search\Comparison;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sql\Alias;

abstract class DeferredColumnTypeFactory extends ColumnFactory
{
    protected ColumnFactoryResolver $column_factory_resolver;

    protected ColumnTypeOptionMap $column_type_option_map;

    protected ComponentFactory\ColumnTypeSelect $column_type_select;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        ColumnFactoryResolver $column_factory_resolver,
        ColumnTypeOptionMap $column_type_option_map,
        ComponentFactory\ColumnTypeSelectResolver $column_type_select_resolver,
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

        $this->column_factory_resolver = $column_factory_resolver;
        $this->column_type_option_map = $column_type_option_map;
        $this->column_type_select = $column_type_select_resolver->resolve(
            $this->get_default_column_type()
        );
    }

    /**
     * Template method allowing subclasses to prepend or append formatters
     */
    protected function apply_formatters(FormatterCollection $formatters, Config $config): FormatterCollection
    {
        return $formatters;
    }

    /**
     * Template method allowing for a default column type
     */
    protected function get_default_column_type(): string
    {
        return DataSource\ColumnType\TextType::class;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        $settings = parent::get_settings($config);
        $settings->add(
            $this->column_type_select->create($config),
            10 - 2 // Art
        );

        return $settings;
    }

    protected function resolve_column_type(Config $config): ColumnFactory
    {
        $value = (string)$config->get(
            $this->column_type_select->get_name(),
            $this->get_default_column_type()
        );

        // Value can be either a ColumnType class name (default) or an option value (from user input)
        $column_type_class = is_subclass_of($value, DataSource\ColumnType::class)
            ? $value
            : $this->column_type_option_map->get_column_type($value);

        // Fallback if $value contains something illegal
        if (! is_subclass_of($column_type_class, DataSource\ColumnType::class)) {
            $column_type_class = DataSource\ColumnType\TextType::class;
        }

        return $this->column_factory_resolver->create(
            $this->id,
            $this->label,
            new $column_type_class(),
            $this->data_source,
            $this->group,
            $this->get_alias()
        );
    }

    public function create(Config $config): Column
    {
        $factory = $this->resolve_column_type($config);
        $column = $factory->create($config);

        if ($column instanceof AdvancedColumn) {
            return new AdvancedColumn(
                $this->get_column_type(),
                $this->label,
                $this->get_settings($config)->merge($column->get_settings()),
                $this->resolve_id($config),
                $column->get_context(),
                $this->apply_formatters($column->get_formatters(), $config),
                $this->get_group(),
                $this->get_description(),
                $this->get_sorting_for_deferred_column($config, $column->sorting()),
                $this->get_editing_for_deferred_column($config, $column->editing()),
                $this->get_search_for_deferred_column($config, $column->search()),
                $this->get_export_for_deferred_column($config, $column->export()),
                $this->get_conditional_format_for_deferred_column($config, $column->conditional_format()),
                $this->get_metric_for_deferred_column($config, $column->metric())
            );
        }

        return parent::create($config);
    }

    protected function get_sorting_for_deferred_column(
        Config $config,
        ?QueryBindings $bindings = null
    ): ?QueryBindings {
        return $bindings;
    }

    protected function get_editing_for_deferred_column(
        Config $config,
        ?Service $service = null
    ): ?Service {
        return $service;
    }

    protected function get_search_for_deferred_column(
        Config $config,
        ?Comparison $comparison = null
    ): ?Comparison {
        return $comparison;
    }

    protected function get_export_for_deferred_column(
        Config $config,
        ?FormatterCollection $formatters = null
    ): ?FormatterCollection {
        return $formatters;
    }

    protected function get_conditional_format_for_deferred_column(
        Config $config,
        ?FormattableConfig $formattable_config = null
    ): ?FormattableConfig {
        return $formattable_config;
    }

    protected function get_metric_for_deferred_column(
        Config $config,
        ?Metric\Config $metric = null
    ): ?Metric\Config {
        return $metric;
    }

}
