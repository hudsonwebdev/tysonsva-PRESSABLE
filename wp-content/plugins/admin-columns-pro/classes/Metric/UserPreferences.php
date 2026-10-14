<?php

declare(strict_types=1);

namespace ACP\Metric;

use AC\Preferences\Preference;
use AC\Preferences\SiteFactory;
use AC\Type\ListScreenId;

class UserPreferences
{
    public const KEY_STICKY = 'sticky';

    public const ALLOWED_KEYS = [
        self::KEY_STICKY,
    ];

    private Preference $storage;

    public function __construct(SiteFactory $preference_factory)
    {
        $this->storage = $preference_factory->create('metric_user_prefs');
    }

    public static function is_valid_key(string $key): bool
    {
        return in_array($key, self::ALLOWED_KEYS, true);
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(ListScreenId $list_id, string $key, $default = null)
    {
        $data = $this->storage->find((string)$list_id) ?: [];

        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    /**
     * @param mixed $value
     */
    public function set(ListScreenId $list_id, string $key, $value): void
    {
        $data = $this->storage->find((string)$list_id) ?: [];

        $data[$key] = $value;

        $this->storage->save((string)$list_id, $data);
    }

    public function delete(ListScreenId $list_id, string $key): void
    {
        $data = $this->storage->find((string)$list_id) ?: [];

        unset($data[$key]);

        $data
            ? $this->storage->save((string)$list_id, $data)
            : $this->storage->delete((string)$list_id);
    }

}
