<?php

declare(strict_types=1);

namespace ACP\Metric\Asset\Script;

use AC\Asset;
use AC\Asset\Location;
use AC\ListScreen;
use AC\Setting\Component;
use ACP\Column\AdvancedColumn;
use ACP\Metric;
use ACP\Metric\PreferenceRepository;
use ACP\Metric\Type\AggregationType;

final class Table extends Asset\Script
{
    private ListScreen $list_screen;

    private PreferenceRepository $preference_repository;

    private Metric\UserPreferences $user_preferences;

    public function __construct(
        string $handle,
        Location $location,
        ListScreen $list_screen,
        PreferenceRepository $preference_repository,
        Metric\UserPreferences $user_preferences
    ) {
        parent::__construct($handle, $location, [], true);

        $this->list_screen = $list_screen;
        $this->preference_repository = $preference_repository;
        $this->user_preferences = $user_preferences;
    }

    private function get_labels(): array
    {
        return [
            AggregationType::AVG                     => __('Average', 'codepress-admin-columns'),
            AggregationType::SUM                     => __('Total', 'codepress-admin-columns'),
            AggregationType::MIN                     => __('Minimum', 'codepress-admin-columns'),
            AggregationType::MAX                     => __('Maximum', 'codepress-admin-columns'),
            AggregationType::RANGE                   => __('Range', 'codepress-admin-columns'),
            AggregationType::DATE_RANGE              => __('Range', 'codepress-admin-columns'),
            AggregationType::DATE_MIN                => __('Earliest', 'codepress-admin-columns'),
            AggregationType::DATE_MAX                => __('Latest', 'codepress-admin-columns'),
            AggregationType::IS_EMPTY                => __('Empty', 'codepress-admin-columns'),
            AggregationType::IS_EMPTY_PERCENTAGE     => __('Empty', 'codepress-admin-columns'),
            AggregationType::IS_NOT_EMPTY            => __('Not empty', 'codepress-admin-columns'),
            AggregationType::IS_NOT_EMPTY_PERCENTAGE => __('Not empty', 'codepress-admin-columns'),
        ];
    }

    private function get_menu_labels(): array
    {
        return array_merge($this->get_labels(), [
            AggregationType::DATE_RANGE              => __('Date Range', 'codepress-admin-columns'),
            AggregationType::DATE_MIN                => __('Earliest Date', 'codepress-admin-columns'),
            AggregationType::DATE_MAX                => __('Latest Date', 'codepress-admin-columns'),
            AggregationType::IS_EMPTY_PERCENTAGE     => __('Empty %', 'codepress-admin-columns'),
            AggregationType::IS_NOT_EMPTY_PERCENTAGE => __('Not empty %', 'codepress-admin-columns'),
        ]);
    }

    private function create_metric_option(AggregationType $aggregation_type, array $labels, array $menu_labels): array
    {
        $value = $aggregation_type->get_value();

        return [
            'value'      => $aggregation_type->get_identifier(),
            'label'      => $aggregation_type->get_label() ?? $labels[$value] ?? $value,
            'menu_label' => $aggregation_type->get_label() ?? $menu_labels[$value] ?? $value,
        ];
    }

    public function register(): void
    {
        parent::register();

        $labels = $this->get_labels();
        $menu_labels = $this->get_menu_labels();

        $columns = [];

        foreach ($this->list_screen->get_columns() as $column) {
            if (! $column instanceof AdvancedColumn) {
                continue;
            }

            $setting = $column->get_setting('metrics');

            if (! $setting instanceof Component) {
                continue;
            }

            if (! $setting->has_input() || $setting->get_input()->get_value() !== 'on') {
                continue;
            }

            $metric = $column->metric();

            if (! $metric) {
                continue;
            }

            $args = [
                'id'         => (string)$column->get_id(),
                'metrics'    => [],
                'preference' => '',
            ];

            foreach ($metric->get_aggregation_types() as $aggregation_type) {
                $args['metrics'][] = $this->create_metric_option($aggregation_type, $labels, $menu_labels);
            }

            $preferred_aggregation_type = $this->preference_repository->find(
                $this->list_screen->get_id(),
                $column->get_id()
            );

            if ($preferred_aggregation_type) {
                $args['preference'] = $preferred_aggregation_type->get_identifier();
            }

            $columns[] = $args;
        }

        $sticky = (bool)$this->user_preferences->get(
            $this->list_screen->get_id(),
            Metric\UserPreferences::KEY_STICKY,
            false
        );

        $this->add_inline_variable('acp_metrics', [
            'columns'       => $columns,
            'sticky_footer' => $sticky,
        ]);

        $this->localize('acp_metrics_i18n', new Asset\Script\Localize\Translation([
            'count'          => __('Count', 'codepress-admin-columns'),
            'percentage'     => __('Percentage', 'codepress-admin-columns'),
            'date'           => __('Date', 'codepress-admin-columns'),
            'calculations'   => __('Calculations', 'codepress-admin-columns'),
            'sticky_enable'  => __('Pin metric bar', 'codepress-admin-columns'),
            'sticky_disable' => __('Unpin metric bar', 'codepress-admin-columns'),
            'calculate'      => __('Calculate', 'codepress-admin-columns'),
            'none'           => __('None', 'codepress-admin-columns'),
        ]));
    }

}
