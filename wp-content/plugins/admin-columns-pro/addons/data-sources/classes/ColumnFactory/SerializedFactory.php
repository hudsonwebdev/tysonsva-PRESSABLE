<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Formatter\ToArray;
use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\NumberFormat;
use AC\Setting\ComponentFactory\SerializedDisplay;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Metric\ColumnType\Existence;
use ACP\Sql\Alias;

class SerializedFactory extends ColumnFactory
{
    use ACP\ConditionalFormat\IntegerFormattableTrait;

    private NumberFormat $number_format;

    private SerializedDisplay $serialized_display;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        SerializedDisplay $serialized_display,
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
        $this->serialized_display = $serialized_display;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->serialized_display->create($config));
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        return parent::get_base_formatters($config)
            ->add(new ToArray());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Existence());
    }

}
