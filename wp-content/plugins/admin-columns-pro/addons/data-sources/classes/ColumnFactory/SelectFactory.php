<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\MapOptionLabel;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\SelectOptions;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource;
use ACA\DataSources\DataSource\ColumnType\SelectColumnType;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search\Comparison\OptionComparison;
use ACA\DataSources\Sorting\OrderByField;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Metric\ColumnType\Choice;
use ACP\Metric\ColumnType\Existence;
use ACP\Sql\Alias;
use JsonException;

class SelectFactory extends ColumnFactory
{
    private SelectOptions $select_options_factory;

    private SelectColumnType $column_type;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        SelectOptions $select_options_factory,
        SelectColumnType $column_type,
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

        $this->select_options_factory = $select_options_factory;
        $this->column_type = $column_type;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->select_options_factory->create($config));
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)
            ->add(new MapOptionLabel($this->get_select_options_factory($config), true));
    }

    private function get_select_options_factory($config): array
    {
        $config_options = $this->column_type->get_options();

        if (count($config_options) > 0) {
            return $config_options;
        }

        $default = [];
        $json = $config->get($this->select_options_factory->get_name(), '');

        try {
            $data = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $data = $default;
        }

        $options = [];

        foreach ($data as $value) {
            $options[$value->value] = $value->label;
        }

        return $options;
    }

    protected function get_sorting(Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        $fields = $this->get_select_options_factory($config);
        natcasesort($fields);

        return new OrderByField($this->id, array_keys($fields));
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new OptionComparison($this->id, $this->get_select_options_factory($config));
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select($this->get_select_options_factory($config)),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        $options = $this->get_select_options_factory($config);

        $column_type = empty($options)
            ? new Existence()
            : new Choice($options, true);

        return new DataSourceField($this->get_column_for_query_result(), $column_type);
    }

}
