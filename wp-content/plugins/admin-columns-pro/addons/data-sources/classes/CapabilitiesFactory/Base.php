<?php

declare(strict_types=1);

namespace ACA\DataSources\CapabilitiesFactory;

use ACA\DataSources\Addon;
use ACA\DataSources\Capabilities;
use ACA\DataSources\CapabilitiesFactory;

final class Base implements CapabilitiesFactory
{
    public function create(): Capabilities
    {
        return new Capabilities(
            Addon::key('read'),
            Addon::key('edit'),
            Addon::key('delete')
        );
    }

}
