<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC\Registerable;
use AC\Request;
use AC\TableScreen;
use ACA\GravityForms\TableScreen\Entry;

final class CaptureRequestDetector implements Registerable
{
    private GridColumnMetaScope $scope;

    public function __construct(GridColumnMetaScope $scope)
    {
        $this->scope = $scope;
    }

    public function register(): void
    {
        add_action('ac/table/screen', [$this, 'maybe_activate'], 5);
    }

    /**
     * Inside an AC capture request (`?save-default-headings=1`), expand GF's
     * grid column meta to include every field so the capture filter chain
     * sees the full column set. A shutdown handler restores the baseline.
     */
    public function maybe_activate(TableScreen $table_screen): void
    {
        if (! $table_screen instanceof Entry) {
            return;
        }

        if ((new Request())->get('save-default-headings') !== '1') {
            return;
        }

        $this->scope->activate_all_columns($table_screen);

        $scope = $this->scope;

        add_action('shutdown', static function () use ($scope, $table_screen): void {
            $scope->restore_to_baseline($table_screen);
        });
    }

}
