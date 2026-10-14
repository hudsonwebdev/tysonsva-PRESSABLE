<?php

declare(strict_types=1);

namespace ACP\Updates;

use AC\Plugin\Version;
use AC\Registerable;
use ACP\AdminColumnsPro;
use ACP\Storage\PluginsDataFactory;
use stdClass;

/**
 * Hooks into the WordPress update process for plugins
 */
class UpdatePlugin implements Registerable
{
    private AdminColumnsPro $plugin;

    private PluginsDataFactory $storage_factory;

    public function __construct(AdminColumnsPro $plugin, PluginsDataFactory $storage_factory)
    {
        $this->plugin = $plugin;
        $this->storage_factory = $storage_factory;
    }

    public function register(): void
    {
        /**
         * For testing purpose use `wp_clean_update_cache()`
         */
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
    }

    public function check_update($transient)
    {
        $data = $this->storage_factory->create()->get();

        if (empty($data) || ! is_array($data)) {
            return $transient;
        }

        $dir_name = $this->plugin->get_dirname();

        if (! isset($data[$dir_name])) {
            return $transient;
        }

        $plugin_data = (object)$data[$dir_name];

        if (null === $transient) {
            $transient = new stdClass();
        }

        if ($this->get_installed_version()->is_lt(new Version($plugin_data->new_version))) {
            $basename = $this->plugin->get_basename();

            $plugin_data->plugin = $basename;

            if (empty($plugin_data->id)) {
                $plugin_data->id = $basename;
            }

            if (empty($plugin_data->package)) {
                $plugin_data->disable_autoupdate = true;
            }

            $transient->response[$basename] = $plugin_data;
        }

        return $transient;
    }

    /**
     * WordPress checks for plugin updates in the same request that installs one, at a point where this
     * process still holds the version from before the update. Read the version that is on disk instead.
     */
    private function get_installed_version(): Version
    {
        $file = WP_PLUGIN_DIR . '/' . $this->plugin->get_basename();

        if (is_file($file) && is_readable($file)) {
            $version = new Version((string)get_file_data($file, ['Version' => 'Version'])['Version']);

            if ($version->is_valid()) {
                return $version;
            }
        }

        return $this->plugin->get_version();
    }

}
