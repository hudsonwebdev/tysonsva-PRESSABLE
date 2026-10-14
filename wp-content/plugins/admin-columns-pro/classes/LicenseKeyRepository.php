<?php

declare(strict_types=1);

namespace ACP;

use AC\Storage\OptionData;
use AC\Storage\OptionDataFactory;
use ACP\Type\LicenseKey;

class LicenseKeyRepository
{
    private OptionData $storage;

    public function __construct(OptionDataFactory $storage_factory)
    {
        $this->storage = $storage_factory->create('acp_subscription_key');
    }

    public function find(): ?LicenseKey
    {
        $key = $this->find_defined();

        if ($key !== null) {
            return $key;
        }

        $stored = (string)$this->storage->get();

        if (! LicenseKey::is_valid($stored)) {
            return null;
        }

        return new LicenseKey($stored);
    }

    /**
     * The key defined in code through the `ACP_LICENCE` constant, which takes
     * precedence over a stored one and cannot be changed from the admin.
     */
    public function find_defined(): ?LicenseKey
    {
        $key = defined('ACP_LICENCE') && constant('ACP_LICENCE')
            ? (string)constant('ACP_LICENCE')
            : '';

        if (! LicenseKey::is_valid($key)) {
            return null;
        }

        return new LicenseKey($key);
    }

    public function delete(): void
    {
        $this->storage->delete();
    }

}
