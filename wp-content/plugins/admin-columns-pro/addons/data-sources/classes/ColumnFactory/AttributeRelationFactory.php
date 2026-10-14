<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\NumberOfItems;
use AC\Setting\ComponentFactory\Separator;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\Addon;
use ACA\DataSources\ColumnFactoryResolver;
use ACA\DataSources\DataSource;
use ACA\DataSources\Editing;
use ACA\DataSources\Formatter\AttributeRelation;
use ACA\DataSources\Metric\Config\AttributeRelationField;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search;
use ACA\DataSources\Setting\ColumnTypeOptionMap;
use ACA\DataSources\Setting\ComponentFactory;
use ACA\DataSources\Sorting;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Sql\Alias;

class AttributeRelationFactory extends DeferredColumnTypeFactory
{
    private DataSource\Relation\AttributeRelation $relation;

    private ComponentFactory\AttributeRelationSelect $attribute_relation_select;

    private NumberOfItems $number_of_items_factory;

    private Separator $separator_factory;

    private Search\DelegatingComparisonFactory $comparison_factory;

    private Search\QueryBindingsFactory\AttributeRelationFactory $query_bindings_factory;

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
        ComponentFactory\AttributeRelationSelectResolver $attribute_relation_select_resolver,
        NumberOfItems $number_of_items_factory,
        Separator $separator_factory,
        DataSource\Relation\AttributeRelation $relation,
        Search\DelegatingComparisonFactory $comparison_factory,
        Search\QueryBindingsFactory\AttributeRelationFactory $query_bindings_factory,
        ?Alias $alias = null
    ) {
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

        $this->relation = $relation;
        $this->number_of_items_factory = $number_of_items_factory;
        $this->separator_factory = $separator_factory;
        $this->attribute_relation_select = $attribute_relation_select_resolver->resolve(
            $this->relation->get_foreign_data_source(),
            $this->relation->get_foreign_attribute()
        );
        $this->comparison_factory = $comparison_factory;
        $this->query_bindings_factory = $query_bindings_factory;
    }

    protected function get_attribute_relation_value(Config $config): ?string
    {
        return $config->get($this->attribute_relation_select->get_name());
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        $formatters = new FormatterCollection();
        $attribute_relation_value = $this->get_attribute_relation_value($config);

        if ($attribute_relation_value) {
            $formatters->add(
                new AttributeRelation(
                    $this->get_column_for_query_result(),
                    $this->relation->get_foreign_value_column(),
                    $attribute_relation_value,
                    $this->relation
                )
            );
        }

        return $formatters;
    }

    protected function apply_formatters(FormatterCollection $formatters, Config $config): FormatterCollection
    {
        if ($this->get_attribute_relation_value($config)) {
            $formatters = $this->get_base_formatters($config)->merge($formatters);
        }

        if ($this->relation->get_cardinality()->is_to_many()) {
            $formatters->add(
                AC\Formatter\Collection\Separator::create_from_settings(
                    $config,
                    $this->separator_factory->get_name(),
                    $this->number_of_items_factory->get_name()
                )
            );
        }

        return $formatters;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        $settings = parent::get_settings($config);

        $settings->add(
            $this->attribute_relation_select->create($config),
            10 - 3 // Poetry
        );

        // Add settings for multiple value management
        if ($this->relation->get_cardinality()->is_to_many()) {
            $settings->add($this->number_of_items_factory->create($config), 20);
            $settings->add($this->separator_factory->create($config), 20);
        }

        return $settings;
    }

    public function get_column_type(): string
    {
        return Addon::slug('attribute-relation-' . $this->id);
    }

    protected function get_sorting_for_deferred_column(
        Config $config,
        ?ACP\Sorting\Model\QueryBindings $bindings = null
    ): ?ACP\Sorting\Model\QueryBindings {
        if (! $bindings instanceof Sorting\QueryBindings) {
            return null;
        }

        $attribute = $this->get_attribute_relation_value($config);

        if (! $attribute) {
            return null;
        }

        return new Sorting\AttributeRelationQueryBindings(
            $bindings,
            $this->relation->get_foreign_value_column(),
            $this->relation,
            $attribute
        );
    }

    protected function get_editing_for_deferred_column(
        Config $config,
        ?ACP\Editing\Service $service = null
    ): ?ACP\Editing\Service {
        $attribute = $this->get_attribute_relation_value($config);

        if (! $service || ! $attribute || $this->relation->get_cardinality()->is_to_many()) {
            return null;
        }

        $view = $service->get_view('inline');

        if (! $view) {
            return null;
        }

        return new ACP\Editing\Service\Basic(
            $view,
            new Editing\Storage\AttributeRelation(
                $this->data_source,
                $this->relation,
                $attribute
            )
        );
    }

    protected function get_search_for_deferred_column(
        Config $config,
        ?ACP\Search\Comparison $comparison = null
    ): ?ACP\Search\Comparison {
        if (! $comparison instanceof Search\Comparison) {
            return null;
        }

        $attribute = $this->get_attribute_relation_value($config);

        if (! $attribute) {
            return null;
        }

        return $this->comparison_factory->create(
            $comparison,
            $this->query_bindings_factory->create(
                $this->relation,
                $this->relation->get_foreign_value_column(),
                $attribute
            )
        );
    }

    protected function get_export_for_deferred_column(
        Config $config,
        ?FormatterCollection $formatters = null
    ): ?FormatterCollection {
        if ($formatters) {
            $formatters = $this->get_base_formatters($config)->merge($formatters);
        }

        return $formatters;
    }

    protected function get_metric_for_deferred_column(
        Config $config,
        ?Metric\Config $metric = null
    ): ?Metric\Config {
        if (! $metric instanceof DataSourceField) {
            return null;
        }

        if ($this->relation->get_cardinality()->is_to_many()) {
            return null;
        }

        $attribute = $this->get_attribute_relation_value($config);

        if (! $attribute) {
            return null;
        }

        return new AttributeRelationField(
            $this->relation,
            $attribute,
            $metric->get_column_type()
        );
    }

}
