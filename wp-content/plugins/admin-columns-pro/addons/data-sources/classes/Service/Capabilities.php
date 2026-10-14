<?php

declare(strict_types=1);

namespace ACA\DataSources\Service;

use AC\Registerable;
use ACA\DataSources;
use WP_Role;

final class Capabilities implements Registerable
{
    private DataSources\Capabilities $default_capabilities;

    private DataSources\CapabilitiesChecker $capabilities_checker;

    private DataSources\DataSourceRegistry $data_sources;

    public function __construct(
        DataSources\CapabilitiesFactory\Base $default_capabilities_factory,
        DataSources\CapabilitiesChecker $capabilities_checker,
        DataSources\DataSourceRegistry $data_sources
    ) {
        $this->default_capabilities = $default_capabilities_factory->create();
        $this->capabilities_checker = $capabilities_checker;
        $this->data_sources = $data_sources;
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'manage_capabilities']);
    }

    public function manage_capabilities(): void
    {
        $default_roles = (array)apply_filters('acp/data-sources/capabilities/roles', ['administrator']);

        foreach ($default_roles as $default_role) {
            $role = get_role($default_role);

            if ($role instanceof WP_Role) {
                foreach ($this->default_capabilities as $default_capability) {
                    if (! $role->has_cap($default_capability)) {
                        $role->add_cap($default_capability);
                    }
                }
            }
        }

        foreach ($this->data_sources as $data_source) {
            do_action(
                'acp/data-sources/capabilities/data-source',
                $data_source,
                $this->capabilities_checker
            );
        }
    }

}
