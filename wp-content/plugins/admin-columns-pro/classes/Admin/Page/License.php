<?php

declare(strict_types=1);

namespace ACP\Admin\Page;

use AC\Admin\RenderableHead;
use AC\Asset;
use AC\Asset\Assets;
use AC\Asset\Style;
use AC\Renderable;
use ACP;
use ACP\AdminColumnsPro;
use ACP\LicenseKeyRepository;
use ACP\Type\Url\AccountFactory;

class License implements Asset\Enqueueables, Renderable, RenderableHead
{
    public const NAME = 'license';

    private Renderable $head;

    private AdminColumnsPro $plugin;

    private AccountFactory $url_factory;

    private LicenseKeyRepository $license_key_repository;

    public function __construct(
        Renderable $head,
        AdminColumnsPro $plugin,
        AccountFactory $url_factory,
        LicenseKeyRepository $license_key_repository
    ) {
        $this->head = $head;
        $this->plugin = $plugin;
        $this->url_factory = $url_factory;
        $this->license_key_repository = $license_key_repository;
    }

    public function render_head(): Renderable
    {
        return $this->head;
    }

    public function get_assets(): Assets
    {
        $location = $this->plugin->get_location();

        $assets = [
            new Style(
                'acp-license-manager',
                $location->with_suffix('assets/core/css/license-manager.css')
            ),
        ];

        $script_location = $location->with_suffix('assets/core/js/admin-page-license.js');

        $assets[] = new ACP\Asset\Script\LicenseManager(
            $script_location,
            $this->plugin->is_network_active(),
            $this->url_factory,
            $this->license_key_repository
        );

        return new Assets($assets);
    }

    public function render(): string
    {
        return '';
    }

}
