<?php

declare(strict_types=1);

namespace ACA\DataSources;

use IteratorAggregate;
use Traversable;

final class Capabilities implements IteratorAggregate
{
    private string $read;

    private string $edit;

    private string $delete;

    public function __construct(
        string $read,
        string $edit,
        string $delete
    ) {
        $this->read = $read;
        $this->edit = $edit;
        $this->delete = $delete;
    }

    public function getIterator(): Traversable
    {
        yield $this->read;
        yield $this->edit;
        yield $this->delete;
    }

    public function get_read(): string
    {
        return $this->read;
    }

    public function get_edit(): string
    {
        return $this->edit;
    }

    public function get_delete(): string
    {
        return $this->delete;
    }

}
