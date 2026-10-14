<?php

declare(strict_types=1);

namespace ACA\DataSources;

use ACA\DataSources\Type\DataSourceId;

final class CapabilitiesChecker
{
    private Capabilities $default_capabilities;

    private array $capabilities = [];

    public function __construct(Capabilities $default_capabilities)
    {
        $this->default_capabilities = $default_capabilities;
    }

    public function register_capabilities(
        DataSourceId $id,
        Capabilities $capabilities
    ): void {
        $this->capabilities[(string)$id] = $capabilities;
    }

    public function get_capabilities(DataSourceId $id): Capabilities
    {
        return $this->capabilities[(string)$id] ?? $this->default_capabilities;
    }

    private function check(string $capability): bool
    {
        return current_user_can($capability);
    }

    public function can_read(DataSourceId $id): bool
    {
        return $this->check($this->get_read($id));
    }

    public function get_read(DataSourceId $id): string
    {
        return $this->get_capabilities($id)->get_read();
    }

    public function can_edit(DataSourceId $id): bool
    {
        return $this->check($this->get_edit($id));
    }

    public function get_edit(DataSourceId $id): string
    {
        return $this->get_capabilities($id)->get_edit();
    }

    public function can_delete(DataSourceId $id): bool
    {
        return $this->check($this->get_delete($id));
    }

    public function get_delete(DataSourceId $id): string
    {
        return $this->get_capabilities($id)->get_delete();
    }

}
