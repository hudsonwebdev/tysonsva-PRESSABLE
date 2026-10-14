<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\DateFormat;
use AC\Setting\ComponentFactory\DateSaveFormat;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search;
use ACA\DataSources\Sorting\QueryBindings;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Metric\ColumnType\Date;
use ACP\Metric\ColumnType\Timestamp;
use ACP\Sorting\Type\DataType;
use ACP\Sql\Alias;

class DateTimeFactory extends ColumnFactory
{
    private DateFormat\Date $date_format_component;

    private DateSaveFormat $date_save_format_component;

    private DataSource\ColumnType\DateTimeType $column_type;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        DateSaveFormat $date_save_format_component,
        DateFormat\Date $date_format_component,
        DataSource\ColumnType\DateTimeType $column_type,
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

        $this->date_format_component = $date_format_component;
        $this->date_save_format_component = $date_save_format_component;
        $this->column_type = $column_type;
    }

    private function get_default_save_date_format(): string
    {
        return DateSaveFormat::FORMAT_DATETIME;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        $settings = parent::get_settings($config);

        if (! $this->column_type->has_date_save_format()) {
            $settings->add(
                $this->date_save_format_component
                    ->with_default($this->get_default_save_date_format())
                    ->create($config)
            );
        }

        $settings->add(
            $this->date_format_component
                ->with_source_format($this->get_source_format($config))
                ->create($config)
        );

        return $settings;
    }

    protected function get_source_format(Config $config): string
    {
        return $this->column_type->has_date_save_format()
            ? $this->column_type->get_date_save_format()
            : $config->get('date_save_format', $this->get_default_save_date_format());
    }

    protected function get_sorting(Config $config): ACP\Sorting\Model\QueryBindings
    {
        return new QueryBindings($this->get_column_for_query(), DataType::create_date_time());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\Comparison\DateTime($this->id);
    }

    protected function get_conditional_format(Config $config): ?ACP\ConditionalFormat\FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(
            new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter('Y-m-d H:i:s')
        );
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\DateTime(),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        $format = $this->get_source_format($config);

        if ($format === DateSaveFormat::FORMAT_UNIX_TIMESTAMP) {
            return new DataSourceField($this->get_column_for_query_result(), new Timestamp());
        }

        if ($format !== DateSaveFormat::FORMAT_DATE && $format !== DateSaveFormat::FORMAT_DATETIME) {
            return null;
        }

        return new DataSourceField($this->get_column_for_query_result(), new Date());
    }

}
