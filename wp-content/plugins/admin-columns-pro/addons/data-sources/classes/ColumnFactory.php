<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC;
use AC\FormatterCollection;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\Formatter\Column;
use ACP\Column\AdvancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\Sql\Alias;

abstract class ColumnFactory extends AdvancedColumnFactory
{
    use ConditionalFormatTrait;

    protected string $id;

    protected string $label;

    protected DataSource $data_source;

    protected Group $group;

    protected ?Alias $alias;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        ?Alias $alias = null
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->data_source = $data_source;
        $this->group = $group;
        $this->alias = $alias;

        parent::__construct($feature_setting_builder_factory, $default_settings_builder);
    }

    protected function get_group(): string
    {
        return $this->group->get_slug();
    }

    public function get_id(): string
    {
        return $this->id;
    }

    protected function get_column_for_query(?string $column = null): string
    {
        if ($column === null) {
            $column = $this->id;
        }

        // Qualify column, unless an alias is present
        $alias = $this->has_alias()
            ? $this->get_alias()
            : $this->data_source->get_repository()->get_table();

        return sprintf('%s.%s', $alias, $column);
    }

    protected function get_column_for_query_result(?string $column = null): string
    {
        if ($column === null) {
            $column = $this->id;
        }

        if ($this->has_alias()) {
            $column = sprintf('%s_%s', $this->alias, $column);
        }

        return $column;
    }

    public function get_label(): string
    {
        return $this->label;
    }

    public function get_column_type(): string
    {
        return Addon::slug($this->get_id());
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return $this->get_formatters($config);
    }

    protected function get_base_formatters(Config $config): FormatterCollection
    {
        return new FormatterCollection([
            new Column($this->get_column_for_query_result()),
        ]);
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return $this->get_base_formatters($config)
                    ->merge(parent::get_formatters($config));
    }

    protected function get_context(Config $config): AC\Column\Context
    {
        return new Setting\Context($config, $this->id);
    }

    public function has_alias(): bool
    {
        return $this->alias instanceof Alias;
    }

    public function get_alias(): ?Alias
    {
        return $this->alias;
    }

}
