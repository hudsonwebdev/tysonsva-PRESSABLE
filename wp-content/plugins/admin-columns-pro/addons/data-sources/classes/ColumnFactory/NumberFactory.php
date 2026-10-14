<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\BeforeAfter;
use AC\Setting\ComponentFactory\NumberFormat;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\ColumnFactory;
use ACA\DataSources\DataSource;
use ACA\DataSources\Editing;
use ACA\DataSources\Metric\Config\DataSourceField;
use ACA\DataSources\Search\Comparison;
use ACA\DataSources\Sorting\QueryBindings;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Metric;
use ACP\Metric\ColumnType\Numeric;
use ACP\Sorting\Type\DataType;
use ACP\Sql\Alias;

class NumberFactory extends ColumnFactory
{
    use ACP\ConditionalFormat\IntegerFormattableTrait;

    private NumberFormat $number_format;

    private BeforeAfter $before_after;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        NumberFormat $number_format,
        BeforeAfter $before_after,
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

        $this->number_format = $number_format;
        $this->before_after = $before_after;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
            ->add($this->number_format->create($config))
            ->add($this->before_after->create($config));
    }

    protected function get_sorting(Config $config): ACP\Sorting\Model\QueryBindings
    {
        return new QueryBindings($this->get_column_for_query(), DataType::create_numeric());
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Comparison\NumberComparison($this->get_column_for_query());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Number(),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return new DataSourceField($this->get_column_for_query_result(), new Numeric());
    }

}
