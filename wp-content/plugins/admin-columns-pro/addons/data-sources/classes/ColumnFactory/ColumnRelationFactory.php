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
use ACA\DataSources\DataSource\Relation;
use ACA\DataSources\Editing;
use ACA\DataSources\Formatter;
use ACA\DataSources\Search;
use ACA\DataSources\Setting\ColumnTypeOptionMap;
use ACA\DataSources\Setting\ComponentFactory;
use ACA\DataSources\Sorting;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sql\Alias;

class ColumnRelationFactory extends DeferredColumnTypeFactory
{
    private Relation $relation;

    private ?ComponentFactory\RelationSelect $relation_select;

    private NumberOfItems $number_of_items_factory;

    private Separator $separator_factory;

    private Search\DelegatingComparisonFactory $comparison_factory;

    private Search\QueryBindingsFactory\RelationFactory $query_bindings_factory;

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
        ComponentFactory\RelationSelectResolver $relation_select_resolver,
        NumberOfItems $number_of_items_factory,
        Separator $separator_factory,
        Relation\ColumnRelation $relation,
        Search\DelegatingComparisonFactory $comparison_factory,
        Search\QueryBindingsFactory\RelationFactory $query_bindings_factory,
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
        $this->relation_select = $relation_select_resolver->resolve($this->relation->get_foreign_data_source());
        $this->number_of_items_factory = $number_of_items_factory;
        $this->separator_factory = $separator_factory;
        $this->comparison_factory = $comparison_factory;
        $this->query_bindings_factory = $query_bindings_factory;
    }

    protected function get_relation_value(Config $config): ?string
    {
        if (! $this->relation_select) {
            return null;
        }

        return $config->get($this->relation_select->get_name());
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        $formatters = new FormatterCollection();
        $column = $this->get_relation_value($config);

        if ($column) {
            $formatters->add(
                new Formatter\Relation(
                    $this->get_column_for_query_result(),
                    $column,
                    $this->relation,
                )
            );
        }

        return $formatters;
    }

    protected function apply_formatters(FormatterCollection $formatters, Config $config): FormatterCollection
    {
        if ($this->get_relation_value($config)) {
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
        if ($this->relation_select) {
            $settings->add(
                $this->relation_select->create($config),
                7
            );
        }

        if ($this->relation->get_cardinality()->is_to_many()) {
            $settings->add($this->number_of_items_factory->create($config), 20);
            $settings->add($this->separator_factory->create($config), 20);
        }

        return $settings;
    }

    public function get_column_type(): string
    {
        return Addon::slug('relation-' . $this->id);
    }

    protected function get_sorting_for_deferred_column(
        Config $config,
        ?QueryBindings $bindings = null
    ): ?QueryBindings {
        if (! $bindings instanceof Sorting\QueryBindings) {
            return null;
        }

        $column = $this->get_relation_value($config);

        if (! $column) {
            return null;
        }

        return new Sorting\RelationQueryBindings(
            $bindings,
            $column,
            $this->relation,
        );
    }

    protected function get_editing_for_deferred_column(
        Config $config,
        ?ACP\Editing\Service $service = null
    ): ?ACP\Editing\Service {
        $column = $this->get_relation_value($config);

        if (! $service || ! $column || $this->relation->get_cardinality()->is_to_many()) {
            return null;
        }

        $view = $service->get_view('inline');

        if (! $view) {
            return null;
        }

        return new ACP\Editing\Service\Basic(
            $view,
            new Editing\Storage\Relation(
                $this->data_source,
                $this->relation,
                $column
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

        $column = $this->get_relation_value($config);

        if (! $column) {
            return null;
        }

        return $this->comparison_factory->create(
            $comparison,
            $this->query_bindings_factory->create(
                $this->relation,
                $column
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
        return null;
    }

}
