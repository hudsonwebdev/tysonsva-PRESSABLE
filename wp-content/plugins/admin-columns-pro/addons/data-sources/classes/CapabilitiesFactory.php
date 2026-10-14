<?php

declare(strict_types=1);

namespace ACA\DataSources;

interface CapabilitiesFactory
{
    public function create(): Capabilities;

}
