<?php

declare(strict_types=1);

namespace ACP\ColumnFactory\NetworkSite;

use AC\FormatterCollection;
use AC\Setting\ComponentCollection;
use AC\Setting\Config;
use AC\Setting\DefaultSettingsBuilder;
use ACP\Column\AdvancedColumnFactory;
use ACP\Column\FeatureSettingBuilderFactory;
use ACP\Formatter\NetworkSite\PostCount;
use ACP\Setting\ComponentFactory;
use ACP\Setting\ComponentFactory\PeriodWithAllTime;

class PostCountFactory extends AdvancedColumnFactory
{
    private ComponentFactory\NetworkSite\PostType $post_type;

    private ComponentFactory\NetworkSite\PostStatus $post_status;

    private PeriodWithAllTime $period;

    public function __construct(
        FeatureSettingBuilderFactory $feature_settings_builder_factory,
        DefaultSettingsBuilder $default_settings_builder,
        ComponentFactory\NetworkSite\PostType $post_type_component,
        ComponentFactory\NetworkSite\PostStatus $post_status,
        PeriodWithAllTime $period
    ) {
        parent::__construct($feature_settings_builder_factory, $default_settings_builder);
        $this->post_type = $post_type_component;
        $this->post_status = $post_status;
        $this->period = $period;
    }

    protected function get_settings(Config $config): ComponentCollection
    {
        return new ComponentCollection([
            $this->post_type->create($config),
            $this->post_status->create($config),
            $this->period->create($config),
        ]);
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return new FormatterCollection([
            new PostCount(
                (string)$config->get('post_type', ''),
                (string)$config->get('post_status', ''),
                $this->get_period_in_days($config)
            ),
        ]);
    }

    private function get_period_in_days(Config $config): ?int
    {
        $period = (string)$config->get('period', '');

        if ('' === $period) {
            return null;
        }

        if ('custom' === $period) {
            $custom = (int)$config->get('period_custom', 0);

            return $custom > 0 ? $custom : null;
        }

        return (int)$period;
    }

    public function get_column_type(): string
    {
        return 'column-msite_postcount';
    }

    public function get_label(): string
    {
        return __('Post Count', 'codepress-admin-columns');
    }

    protected function get_export(Config $config): ?FormatterCollection
    {
        return null;
    }

}
