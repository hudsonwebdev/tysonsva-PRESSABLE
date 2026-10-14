<?php

declare(strict_types=1);

namespace ACA\DataSources\CapabilitiesFactory;

use ACA\DataSources\Capabilities;
use ACA\DataSources\CapabilitiesFactory;
use ACA\DataSources\DataSource;

/**
 * This decorator maps capabilities to a single DataSource, allows for fine-grained capabilities.
 */
final class Mapped implements CapabilitiesFactory
{
    private CapabilitiesFactory $factory;

    private DataSource $data_source;

    public function __construct(CapabilitiesFactory $factory, DataSource $data_source)
    {
        $this->factory = $factory;
        $this->data_source = $data_source;
    }

    public function create(): Capabilities
    {
        $capabilities = $this->factory->create();
        $mapped = [];

        foreach ($capabilities as $capability) {
            $mapped[] = $capability . '_' . $this->data_source->get_id();
        }

        return new Capabilities(...$mapped);
    }

}
