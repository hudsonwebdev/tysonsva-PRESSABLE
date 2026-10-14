<?php

declare(strict_types=1);

namespace ACA\DataSources\ColumnFactory;

use AC\Setting\ComponentCollection;
use AC\Setting\ComponentFactory\LinkLabel;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use AC\Type\Group;
use ACA\DataSources\DataSource;
use ACA\DataSources\Editing;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Sql\Alias;

class UrlFactory extends StringFactory
{
    private LinkLabel $link_label;

    public function __construct(
        FeatureSettingBuilderFactory $feature_setting_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        string $id,
        string $label,
        DataSource $data_source,
        Group $group,
        LinkLabel $link_label,
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

        $this->link_label = $link_label;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return parent::get_settings($config)
                     ->add($this->link_label->create($config));
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Url())->set_clear_button(true),
            new Editing\Storage\Column($this->data_source, $this->id)
        );
    }

}
