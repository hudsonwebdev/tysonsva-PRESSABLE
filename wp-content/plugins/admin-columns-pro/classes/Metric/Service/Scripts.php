<?php

declare(strict_types=1);

namespace ACP\Metric\Service;

use AC;
use AC\Asset\Style;
use AC\Registerable;
use ACP\Metric;
use ACP\Metric\Asset\Script\Table;
use ACP\Metric\PreferenceRepository;
use ACP\Table\TableSupport;

class Scripts implements Registerable
{
    private array $scripts = [];

    private AC\Asset\Location\Absolute $location;

    private PreferenceRepository $preference_repository;

    private Metric\UserPreferences $user_preferences;

    public function __construct(
        AC\Asset\Location\Absolute $location,
        PreferenceRepository $preference_repository,
        Metric\UserPreferences $user_preferences
    ) {
        $this->location = $location;
        $this->preference_repository = $preference_repository;
        $this->user_preferences = $user_preferences;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', function (AC\ListScreen $list_screen) {
            if (! TableSupport::is_metric_enabled($list_screen)) {
                return;
            }
            $this->scripts[] = new Table(
                'acp-metrics-table',
                $this->location->with_suffix('assets/metric/js/table.js'),
                $list_screen,
                $this->preference_repository,
                $this->user_preferences
            );
            $this->scripts[] = new Style(
                'acp-metrics-style-table',
                $this->location->with_suffix('assets/metric/css/table.css')
            );
        });

        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts(): void
    {
        foreach ($this->scripts as $script) {
            $script->enqueue();
        }
    }

}
