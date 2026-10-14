<?php

declare(strict_types=1);

namespace ACP\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Nonce\Ajax;

class PluginUpdatesCheck extends Script
{
    public function __construct(Location $location)
    {
        parent::__construct('acp-plugins-update-check', $location);
    }

    public function register(): void
    {
        parent::register();

        $this->add_inline_variable(
            'ACP_PLUGIN_UPDATES_CHECK',
            [
                'nonce' => (new Ajax())->create(),
            ]
        );
    }

}
