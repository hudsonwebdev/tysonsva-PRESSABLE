<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\BeforeAfter;
use AC\Setting\ComponentFactory\StringLimit;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\DataSource;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Sql\Alias;

class TextFactory extends StringFactory
{
    private StringLimit $string_limit;

    private BeforeAfter $before_after;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        StringLimit $string_limit,
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

        $this->string_limit = $string_limit;
        $this->before_after = $before_after;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
                     ->add($this->string_limit->create($config))
                     ->add($this->before_after->create($config));
    }

}
