<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter;
use AC\FormatterCollection;
use AC\Helper\Select\Option;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\BooleanValues;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use AC\Type\ToggleOptions;
use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\ColumnType\BooleanType;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search\Comparison\ToggleComparison;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Metric\ColumnType\Choice;
use ACP\Sql\Alias;

class BooleanFactory extends StringFactory
{
    private BooleanValues $boolean_values;

    private BooleanType $column_type;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        BooleanValues $boolean_values,
        BooleanType $column_type,
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
        $this->boolean_values = $boolean_values;
        $this->column_type = $column_type;
    }

    public function get_boolean_values(Config $config): ToggleOptions
    {
        if ($this->column_type->has_options()) {
            return $this->column_type->get_options();
        }

        $off_value = $config->get('boolean_value_off', '0');
        $on_value = $config->get('boolean_value_on', '1');

        return new ToggleOptions(
            new Option($off_value, $off_value),
            new Option($on_value, $on_value),
        );
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->boolean_values->create($config));
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Toggle(
                $this->get_boolean_values($config)
            ),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ToggleComparison($this->id, $this->get_boolean_values($config));
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new Formatter\ToggleValue($this->get_boolean_values($config)));
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        $options = $this->get_boolean_values($config);

        $choices = [
            $options->get_disabled()->get_value() => $options->get_disabled()->get_label(),
            $options->get_enabled()->get_value()  => $options->get_enabled()->get_label(),
        ];

        return new DataSourceField(
            $this->get_column_for_query_result(),
            new Choice($choices, true)
        );
    }

}
