<?php

declare(strict_types=1);

namespace ACP\Admin;

use AC\Asset;
use AC\Registerable;
use ACP\ActivationTokenFactory;
use ACP\AdminColumnsPro;
use ACP\Asset\Script;
use ACP\Transient\TimeTransientFactory;

class Scripts implements Registerable
{
    private ActivationTokenFactory $token_factory;

    private Asset\Location\Absolute $location;

    public function __construct(
        AdminColumnsPro $plugin,
        ActivationTokenFactory $activation_token_factory
    ) {
        $this->location = $plugin->get_location();
        $this->token_factory = $activation_token_factory;
    }

    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'register_daily_license_check']);
    }

    public function register_daily_license_check(): void
    {
        $activation_token = $this->token_factory->create();

        if (! $activation_token) {
            return;
        }

        $cache = TimeTransientFactory::create_license_check_daily();

        if ($cache->is_expired()) {
            $location = $this->location->with_suffix('assets/core/js/license-check.js');

            $script = new Script\LicenseCheck($location);
            $script->enqueue();
        }
    }

}
